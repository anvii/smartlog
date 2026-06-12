<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Override;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;

class ClientsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Client::factory(10)->create();
    }

    /**
     * Check if clients table available
     */
    public function testClients(): void
    {
        $response = $this->get('/api/clients');
        $response->assertStatus(200);
        $clients = json_decode($response->getContent());
        $this->assertCount(10, $clients);
    }

}
