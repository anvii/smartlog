<?php

namespace Tests\Feature\Jobs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SendMessageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testConnection(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
