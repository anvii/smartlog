<?php

namespace Tests\Unit;

use App\Models\Message;
use App\Models\MessageTrack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public $payload = [
        'client_id' => 1,
        'channel' => 'email',
        'body' => 'Some message body',
        'priority' => 'normal',
    ];

    /**
     * Test creating a message
     */
    public function testCreate(): void
    {
        $message = Message::create($this->payload);
        
        $this->assertNotEmpty($message->id, 'Message id is empty');
        $this->assertEquals('created', $message->status, 'Message status must be new');
    }

    public function testChangeStatus(): void
    {
        $message = Message::create($this->payload);
        
        $this->assertTrue($message->update(['status'=>'queued']), 'Change status');
    }

    /**
     * Test message tracking
     */
    public function testTracking(): void
    {
        $message = Message::create($this->payload);

        $tracks = MessageTrack::count();
        $this->assertEquals(1, $tracks);

        $message->update(['status'=>'queued']);
        $tracks = MessageTrack::count();
        $this->assertEquals(2, $tracks);
    }
}
