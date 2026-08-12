<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--force' => true]);
    }

    public function test_public_website_page_loads_successfully()
    {
        $response = $this->get('/website');

        $response->assertStatus(200);
        $response->assertSee('MS ERP Studio');
        $response->assertSee('Experience 10 Design Paradigms Live');
        $response->assertSee('Bento Grid');
    }
}
