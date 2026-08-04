<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Services\Email\EmailSyncService;
use App\Http\Controllers\EmailController;

class EmailServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmailSyncService $syncService;
    protected object $account;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->syncService = new EmailSyncService();

        // 1. Setup default roles and user
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $user = \App\Models\User::factory()->create([
            'role_id' => $roleId,
        ]);
        $this->actingAs($user);

        // 2. Run self-healing schema creation
        $controller = app(EmailController::class);
        $controller->ensureEmailTablesExist();

        // 3. Create active email account
        $accountId = DB::table('email_accounts')->insertGetId([
            'user_id' => $user->id,
            'email' => 'test-user@mserp.local',
            'display_name' => 'Test User',
            'smtp_host' => 'localhost',
            'smtp_port' => 1025,
            'is_active' => true,
            'is_live_sync_enabled' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert pivot mapping
        DB::table('email_account_users')->insert([
            'email_account_id' => $accountId,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->account = DB::table('email_accounts')->where('id', $accountId)->first();

        Storage::fake('local');
    }

    /**
     * Test self-healing schema ensures all required columns are created.
     */
    public function test_email_tables_schema_columns_exist(): void
    {
        $this->assertTrue(Schema::hasColumn('emails', 'in_reply_to'));
        $this->assertTrue(Schema::hasColumn('emails', 'references'));
        $this->assertTrue(Schema::hasColumn('email_attachments', 'sha256'));
    }

    /**
     * Test Content-Addressable Storage (CAS) saves a single physical file on disk
     * for identical attachments but links them at distinct human-readable paths.
     */
    public function test_attachment_cas_deduplication_and_linking(): void
    {
        // 1. Create two separate emails
        $emailId1 = DB::table('emails')->insertGetId([
            'email_account_id' => $this->account->id,
            'message_id' => 'msg1@mserp.local',
            'thread_id' => 'thread-uuid-1',
            'from_address' => 'sender@domain.local',
            'to_address' => 'test-user@mserp.local',
            'subject' => 'Mail 1',
            'folder' => 'INBOX',
            'created_at' => now(),
        ]);

        $emailId2 = DB::table('emails')->insertGetId([
            'email_account_id' => $this->account->id,
            'message_id' => 'msg2@mserp.local',
            'thread_id' => 'thread-uuid-2',
            'from_address' => 'sender@domain.local',
            'to_address' => 'test-user@mserp.local',
            'subject' => 'Mail 2',
            'folder' => 'INBOX',
            'created_at' => now(),
        ]);

        // 2. Save same attachment content for both emails
        $fileContent = 'This is a heavy business report pdf file content.';
        $fileName = 'business_report.pdf';
        $mimeType = 'application/pdf';
        $dateSent = '2026-06-21 12:00:00';
        $sha256 = hash('sha256', $fileContent);

        $attId1 = $this->syncService->saveAttachmentCas($fileContent, $fileName, $mimeType, $dateSent, 'msg1@mserp.local', $emailId1);
        $attId2 = $this->syncService->saveAttachmentCas($fileContent, $fileName, $mimeType, $dateSent, 'msg2@mserp.local', $emailId2);

        // 3. Assert DB entries are registered
        $this->assertDatabaseHas('email_attachments', ['id' => $attId1, 'sha256' => $sha256]);
        $this->assertDatabaseHas('email_attachments', ['id' => $attId2, 'sha256' => $sha256]);

        // 4. Assert CAS physical file exists
        Storage::assertExists("email_attachments/cas/{$sha256}");

        // 5. Assert human-readable paths are registered and files exist (linked or copies)
        $att1 = DB::table('email_attachments')->where('id', $attId1)->first();
        $att2 = DB::table('email_attachments')->where('id', $attId2)->first();

        $this->assertNotEquals($att1->file_path, $att2->file_path);
        Storage::assertExists($att1->file_path);
        Storage::assertExists($att2->file_path);
    }

    /**
     * Test account-wide duplicate email checks prevent duplicate message IDs
     * across folder boundaries.
     */
    public function test_account_wide_message_deduplication(): void
    {
        $messageId = 'msg-duplicate-123@domain.local';

        // 1. Sync in SENT folder
        $emailIdSent = $this->syncService->saveParsedEmail($this->account, [
            'message_id' => $messageId,
            'subject' => 'Self Sent Mail',
            'from_address' => 'test-user@mserp.local',
            'to_address' => 'test-user@mserp.local',
            'date_sent' => '2026-06-21 12:00:00',
            'body_html' => '<p>Hello myself</p>',
            'body_text' => 'Hello myself',
        ], null, 'SENT');

        // 2. Sync in INBOX folder
        $emailIdInbox = $this->syncService->saveParsedEmail($this->account, [
            'message_id' => $messageId,
            'subject' => 'Self Sent Mail',
            'from_address' => 'test-user@mserp.local',
            'to_address' => 'test-user@mserp.local',
            'date_sent' => '2026-06-21 12:00:00',
            'body_html' => '<p>Hello myself</p>',
            'body_text' => 'Hello myself',
        ], null, 'INBOX');

        // 3. Assert single record exists and ID matches
        $this->assertEquals($emailIdSent, $emailIdInbox);
        
        $this->assertDatabaseHas('emails', [
            'id' => $emailIdSent,
            'message_id' => $messageId,
            'folder' => 'INBOX',
        ]);
        
        $count = DB::table('emails')
            ->where('email_account_id', $this->account->id)
            ->where('message_id', $messageId)
            ->count();
        $this->assertEquals(1, $count);
    }

    /**
     * Test RFC threading correctly matches thread ID using In-Reply-To and References tree.
     */
    public function test_rfc_header_threading_hierarchy(): void
    {
        // 1. Sync parent email
        $parentMsgId = 'parent-msg-id-123@domain.local';
        $parentId = $this->syncService->saveParsedEmail($this->account, [
            'message_id' => $parentMsgId,
            'subject' => 'RFC Thread Threading Root',
            'from_address' => 'customer@domain.local',
            'to_address' => 'test-user@mserp.local',
            'date_sent' => '2026-06-21 10:00:00',
            'body_html' => '<p>Question</p>',
            'body_text' => 'Question',
        ], null, 'INBOX');

        $parentEmail = DB::table('emails')->where('id', $parentId)->first();
        $this->assertNotEmpty($parentEmail->thread_id);

        // 2. Sync child email (replying to parent)
        $childMsgId = 'child-msg-id-456@domain.local';
        $childId = $this->syncService->saveParsedEmail($this->account, [
            'message_id' => $childMsgId,
            'subject' => 'Re: RFC Thread Threading Root',
            'from_address' => 'test-user@mserp.local',
            'to_address' => 'customer@domain.local',
            'date_sent' => '2026-06-21 11:00:00',
            'body_html' => '<p>Answer</p>',
            'body_text' => 'Answer',
            'in_reply_to' => $parentMsgId,
        ], null, 'SENT');

        $childEmail = DB::table('emails')->where('id', $childId)->first();
        
        // Assert child resolves to same thread ID
        $this->assertEquals($parentEmail->thread_id, $childEmail->thread_id);

        // 3. Sync grandchild email (with References header chain)
        $grandchildMsgId = 'grandchild-msg-id-789@domain.local';
        $grandchildId = $this->syncService->saveParsedEmail($this->account, [
            'message_id' => $grandchildMsgId,
            'subject' => 'Re: RFC Thread Threading Root',
            'from_address' => 'customer@domain.local',
            'to_address' => 'test-user@mserp.local',
            'date_sent' => '2026-06-21 11:30:00',
            'body_html' => '<p>Follow-up</p>',
            'body_text' => 'Follow-up',
            'in_reply_to' => $childMsgId,
            'references' => "<{$parentMsgId}> <{$childMsgId}>",
        ], null, 'INBOX');

        $grandchildEmail = DB::table('emails')->where('id', $grandchildId)->first();

        // Assert grandchild resolves to same thread ID
        $this->assertEquals($parentEmail->thread_id, $grandchildEmail->thread_id);
    }

    /**
     * Test background artisan command email:sync and auto-sync API endpoint.
     */
    public function test_artisan_email_sync_command_and_auto_sync_endpoint(): void
    {
        // 1. Run artisan command
        $exitCode = \Illuminate\Support\Facades\Artisan::call('email:sync', [
            '--account' => $this->account->id
        ]);
        $this->assertEquals(0, $exitCode);

        // 2. Call auto-sync endpoint via HTTP
        $response = $this->getJson('/api/email/auto-sync');
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ])
                 ->assertJsonStructure([
                     'success',
                     'new_emails_count',
                     'folder_counts'
                 ]);
    }

    /**
     * Test multi-user account isolation prevents email mix-matching across 2000 users / 1500 accounts.
     */
    public function test_multi_user_account_isolation_prevents_email_leak(): void
    {
        // Create User B and Account B
        $userB = \App\Models\User::factory()->create(['role_id' => 1]);
        $accountBId = DB::table('email_accounts')->insertGetId([
            'user_id' => $userB->id,
            'email' => 'user-b@mserp.local',
            'display_name' => 'User B',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('email_account_users')->insert([
            'email_account_id' => $accountBId,
            'user_id' => $userB->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert sensitive email for User B / Account B
        DB::table('emails')->insert([
            'email_account_id' => $accountBId,
            'message_id' => 'secret-b@mserp.local',
            'thread_id' => 'secret-thread-b',
            'from_address' => 'private@vendor.com',
            'to_address' => 'user-b@mserp.local',
            'subject' => 'CONFIDENTIAL USER B DATA',
            'folder' => 'INBOX',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Acting as $this->user (User A)
        $response = $this->getJson('/api/email/list');
        $response->assertStatus(200);

        $emails = $response->json('data');
        $subjects = array_column($emails, 'subject');

        // User A must NEVER see User B's emails!
        $this->assertNotContains('CONFIDENTIAL USER B DATA', $subjects);
    }

    /**
     * Test DB column is_live_sync_enabled controls auto-sync polling
     * and message_id bracket cleaning prevents duplicate message rows.
     */
    public function test_live_sync_db_column_toggle_and_message_id_deduplication(): void
    {
        session(['active_email_account_id' => $this->account->id]);

        // 1. Test live sync toggle API endpoint
        $response = $this->postJson('/api/email/toggle-live-sync');
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'is_live_sync_enabled' => false
                 ]);

        $account = DB::table('email_accounts')->where('id', $this->account->id)->first();
        $this->assertFalse((bool)$account->is_live_sync_enabled);

        // 2. Test auto-sync respects disabled flag
        $syncResponse = $this->getJson('/api/email/auto-sync');
        $syncResponse->assertStatus(200)
                    ->assertJson([
                        'success' => true,
                        'is_live_sync_enabled' => false,
                        'new_emails_count' => 0
                    ]);

        // 3. Test message_id bracket cleaning prevents duplicate records
        $rawIdWithBrackets = '<unique-msg-999@domain.local>';
        $cleanId = 'unique-msg-999@domain.local';

        $emailId1 = $this->syncService->saveParsedEmail($this->account, [
            'message_id' => $rawIdWithBrackets,
            'subject' => 'Test Deduplication',
            'from_address' => 'sender@domain.local',
            'to_address' => 'test-user@mserp.local',
            'date_sent' => '2026-07-30 12:00:00',
            'body_html' => '<p>Original body</p>',
            'body_text' => 'Original body',
        ], null, 'INBOX');

        $emailId2 = $this->syncService->saveParsedEmail($this->account, [
            'message_id' => $cleanId,
            'subject' => 'Test Deduplication',
            'from_address' => 'sender@domain.local',
            'to_address' => 'test-user@mserp.local',
            'date_sent' => '2026-07-30 12:00:00',
            'body_html' => '<p>Updated body</p>',
            'body_text' => 'Updated body',
        ], null, 'INBOX');

        // Must update existing record instead of inserting duplicate!
        $this->assertEquals($emailId1, $emailId2);

        $count = DB::table('emails')
            ->where('email_account_id', $this->account->id)
            ->where('folder', 'INBOX')
            ->where(function($q) use ($cleanId) {
                $q->where('message_id', $cleanId)->orWhere('message_id', '<' . $cleanId . '>');
            })
            ->count();
        $this->assertEquals(1, $count);
    }

    /**
     * Test manual sync endpoint POST /api/email/sync/{id} works cleanly with CSRF token.
     */
    public function test_manual_sync_endpoint_with_csrf(): void
    {
        session(['active_email_account_id' => $this->account->id]);

        $response = $this->postJson('/api/email/sync/' . $this->account->id);
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);
    }

    /**
     * Test sending email with attachment processes file paths using Storage::path.
     */
    public function test_send_email_with_attachments_path_resolution(): void
    {
        session(['active_email_account_id' => $this->account->id]);

        $fakeFile = \Illuminate\Http\Testing\File::create('invoice.pdf', 500, 'application/pdf');

        $response = $this->post('/api/email/send', [
            'to' => 'recipient@mserp-client.com',
            'subject' => 'Invoice Attached Test',
            'body_html' => '<p>Please see invoice attached.</p>',
            'attachments' => [$fakeFile]
        ], [
            'X-CSRF-TOKEN' => csrf_token(),
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(200);

        // Verify email was created in SENT folder with has_attachments = true
        $sentEmail = DB::table('emails')
            ->where('email_account_id', $this->account->id)
            ->where('subject', 'Invoice Attached Test')
            ->first();

        $this->assertNotNull($sentEmail);
        $this->assertTrue((bool)$sentEmail->has_attachments);

        // Verify attachment recorded in email_attachments table
        $attachment = DB::table('email_attachments')
            ->where('email_id', $sentEmail->id)
            ->where('filename', 'invoice.pdf')
            ->first();

        $this->assertNotNull($attachment);
        Storage::assertExists($attachment->file_path);
    }

    /**
     * Test separate standalone emails receive distinct thread IDs and are not lumped together by substring matching.
     */
    public function test_standalone_emails_receive_distinct_thread_ids(): void
    {
        $id1 = $this->syncService->saveParsedEmail($this->account, [
            'message_id' => 'standalone-msg-1@domain.local',
            'subject' => 'Test Email Subject',
            'from_address' => 'client1@domain.local',
            'to_address' => 'test-user@mserp.local',
            'date_sent' => '2026-07-31 10:00:00',
            'body_html' => '<p>Standalone 1</p>',
            'body_text' => 'Standalone 1',
        ], null, 'INBOX');

        $id2 = $this->syncService->saveParsedEmail($this->account, [
            'message_id' => 'standalone-msg-2@domain.local',
            'subject' => 'Test Email Subject Revision',
            'from_address' => 'client2@domain.local',
            'to_address' => 'test-user@mserp.local',
            'date_sent' => '2026-07-31 11:00:00',
            'body_html' => '<p>Standalone 2</p>',
            'body_text' => 'Standalone 2',
        ], null, 'INBOX');

        $email1 = DB::table('emails')->where('id', $id1)->first();
        $email2 = DB::table('emails')->where('id', $id2)->first();

        // Standalone emails without In-Reply-To / Re: prefix must have DIFFERENT thread IDs!
        $this->assertNotEquals($email1->thread_id, $email2->thread_id);
    }

    /**
     * Test saving draft with attachments saves attachments to storage and database.
     */
    public function test_save_draft_with_attachments(): void
    {
        session(['active_email_account_id' => $this->account->id]);

        $fakeFile = \Illuminate\Http\Testing\File::create('draft_doc.pdf', 300, 'application/pdf');

        $response = $this->post('/api/email/save-draft', [
            'to' => 'draft_recipient@mserp.local',
            'subject' => 'Draft With Attachment',
            'body_html' => '<p>Draft body html</p>',
            'attachments' => [$fakeFile]
        ], [
            'X-CSRF-TOKEN' => csrf_token(),
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $draftId = $response->json('draft_id');
        $this->assertNotNull($draftId);

        $draftEmail = DB::table('emails')->where('id', $draftId)->first();
        $this->assertEquals('DRAFTS', $draftEmail->folder);
        $this->assertTrue((bool)$draftEmail->has_attachments);

        $attachment = DB::table('email_attachments')
            ->where('email_id', $draftId)
            ->where('filename', 'draft_doc.pdf')
            ->first();

        $this->assertNotNull($attachment);
        Storage::assertExists($attachment->file_path);
    }

    /**
     * Test server lookup config returns dynamic settings for custom domain (@mserp.in).
     */
    public function test_lookup_server_config_for_custom_domain(): void
    {
        $response = $this->getJson('/api/email-accounts/lookup-config?email=user@mserp.in');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'config' => [
                         'domain' => 'mserp.in',
                         'imap_host' => 'mail.mserp.in',
                         'imap_port' => 993,
                         'imap_encryption' => 'ssl',
                         'smtp_host' => 'mail.mserp.in',
                         'smtp_port' => 587,
                         'smtp_encryption' => 'starttls',
                     ]
                 ]);
    }

    /**
     * Test store custom email account with custom ports and STARTTLS/SSL.
     */
    public function test_store_custom_email_account(): void
    {
        $payload = [
            'email' => 'admin@mserp.in',
            'display_name' => 'MS ERP Admin',
            'password' => 'secret123',
            'imap_host' => 'mail.mserp.in',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'smtp_host' => 'mail.mserp.in',
            'smtp_port' => 587,
            'smtp_encryption' => 'starttls',
        ];

        $response = $this->postJson('/api/email-accounts/store', $payload);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $account = DB::table('email_accounts')->where('email', 'admin@mserp.in')->first();
        $this->assertNotNull($account);
        $this->assertEquals('mail.mserp.in', $account->imap_host);
        $this->assertEquals(993, $account->imap_port);
        $this->assertEquals('ssl', $account->imap_encryption);
        $this->assertEquals('mail.mserp.in', $account->smtp_host);
        $this->assertEquals(587, $account->smtp_port);
        $this->assertEquals('starttls', $account->smtp_encryption);
    }

    /**
     * Test live account connection test endpoint in placeholder / simulation mode.
     */
    public function test_test_account_connection_endpoint(): void
    {
        $payload = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'imap_host' => 'localhost',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'smtp_host' => 'localhost',
            'smtp_port' => 587,
            'smtp_encryption' => 'starttls',
        ];

        $response = $this->postJson('/api/email-accounts/test-connection', $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'imap_ok' => true,
                     'smtp_ok' => true
                 ]);
    }
}

