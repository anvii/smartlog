<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public $payload = [
        'client_id' => [1, 2, 3],
        'channel' => 'email',
        'body' => 'Message body',
        'priority' => 'normal',
    ];

    /**
     * Test sending message
     */
    public function testPostMessage(): void
    {
        $response = $this->post('/api/message/post', $this->payload);
        $response->assertStatus(200);

        $response = $this->get('/api/messages');
        $messages = json_decode($response->getContent());
        $this->assertCount(3, $messages);
        $this->assertEquals('queued', $messages[0]->status);
    }

    public function testFilterMessages(): void
    {
        $response = $this->post('/api/message/post', $this->payload);
        $response->assertStatus(200);

        $response = $this->get('/api/messages?client_id=1');
        $messages = json_decode($response->getContent());
        $this->assertCount(1, $messages);

        $response = $this->get('/api/messages?priority=normal');
        $messages = json_decode($response->getContent());
        $this->assertCount(3, $messages);

        $response = $this->get('/api/messages?status=queued');
        $messages = json_decode($response->getContent());
        $this->assertCount(3, $messages);
    }

    /**
     * Test if error returned if sending duplicated messages
     */
    public function testDuplicatedMessages(): void
    {
        $response = $this->post('/api/message/post', $this->payload);
        $response->assertStatus(200);

        // Send duplicates
        $response = $this->post('/api/message/post', $this->payload);
        $response->assertStatus(500);
    }

    public function testChangeStatus(): void
    {
        $response = $this->post('/api/message/post', $this->payload);

        $response = $this->get('/api/messages');
        $messages = json_decode($response->getContent());

        $payload = [
            'message_id' => $messages[0]->id,
            'status' => 'error',
            'error' => 'Some message',
        ];
        $this->post('/api/message/status', $payload);

        $response = $this->get('/api/messages');
        $messages = json_decode($response->getContent());

        $this->assertEquals($messages[0]->status, $payload['status']);
        $this->assertEquals($messages[0]->error, $payload['error']);

        $response = $this->get('/api/message/track?message_id=' . $messages[0]->id);
        $tracks = json_decode($response->getContent());
        $this->assertCount(3, $tracks);
    }
}
