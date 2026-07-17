<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;

class ChatSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $repUser;
    protected $groupId;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Seed base configuration
        $this->artisan('db:seed');

        // Resolve seeded references
        $this->adminUser = User::where('email', 'admin@mserp.com')->first();
        $this->repUser = User::where('email', 'rep.north1@mserp.com')->first();

        // Create a test chat group
        $this->groupId = DB::table('chat_groups')->insertGetId([
            'name' => 'General Channel',
            'created_by' => $this->adminUser->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Join both users to the group
        DB::table('chat_group_users')->insert([
            ['group_id' => $this->groupId, 'user_id' => $this->adminUser->id, 'created_at' => now(), 'updated_at' => now()],
            ['group_id' => $this->groupId, 'user_id' => $this->repUser->id, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Test retrieving chat context loads WhatsApp metadata correctly.
     */
    public function test_chat_context_returns_last_message_and_unread_counts()
    {
        // 1. Insert a direct message from Sales Rep to Admin (unread)
        DB::table('chat_messages')->insert([
            'sender_id' => $this->repUser->id,
            'recipient_id' => $this->adminUser->id,
            'message' => 'Hello Admin, this is a test message.',
            'is_read' => false,
            'created_at' => now()->subMinutes(5),
            'updated_at' => now()->subMinutes(5)
        ]);

        // 2. Fetch context as Admin
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/chat/context');

        $response->assertStatus(200);
        $data = $response->json();

        // Assert contacts include the Rep user and metadata is computed
        $this->assertNotEmpty($data['contacts']);
        $repContact = collect($data['contacts'])->firstWhere('id', $this->repUser->id);
        
        $this->assertNotNull($repContact);
        $this->assertEquals('Hello Admin, this is a test message.', $repContact['last_message']);
        $this->assertEquals(1, $repContact['unread_count']);
    }

    /**
     * Test sending a message saves it to the database.
     */
    public function test_send_message_saves_correctly()
    {
        $payload = [
            'recipient_id' => $this->repUser->id,
            'message' => 'Hey Sales Rep!'
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/chat/send', $payload);

        $response->assertStatus(200);
        $this->assertTrue(DB::table('chat_messages')
            ->where('sender_id', $this->adminUser->id)
            ->where('recipient_id', $this->repUser->id)
            ->where('message', 'Hey Sales Rep!')
            ->exists());
    }

    /**
     * Test loading messages automatically marks them as read.
     */
    public function test_load_messages_marks_unread_as_read()
    {
        // 1. Insert unread message from Rep to Admin
        $msgId = DB::table('chat_messages')->insertGetId([
            'sender_id' => $this->repUser->id,
            'recipient_id' => $this->adminUser->id,
            'message' => 'Please review this.',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 2. Assert is_read is false initially
        $msgBefore = DB::table('chat_messages')->where('id', $msgId)->first();
        $this->assertEquals(0, $msgBefore->is_read);

        // 3. Load conversation as Admin
        $response = $this->actingAs($this->adminUser)
            ->getJson("/api/chat/messages?recipient_id={$this->repUser->id}");

        $response->assertStatus(200);

        // 4. Assert is_read is now true
        $msgAfter = DB::table('chat_messages')->where('id', $msgId)->first();
        $this->assertEquals(1, $msgAfter->is_read);
    }
}
