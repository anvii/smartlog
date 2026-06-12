<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Junges\Kafka\Facades\Kafka;

class SendMessage implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public $uniqueFor = 3600*24;
    public $tries = 0;

    /**
     * Create a new job instance.
     */
    public function __construct(public Message $message)
    {
        // This forces Laravel to wait until the DB transaction commits before processing the job
        $this->afterCommit = true;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $topic = $this->message->priority;

        try {
            Kafka::publish()
                ->onTopic($topic)
                ->withBody($this->message)
                ->send();
        }
        catch(\Exception $e) {
            $this->message->update(['status'=>'error', 'error'=>$e->getMessage()]);
            throw $e;
        }

        $this->message->update(['status'=>'queued']);
    }

    public function uniqueId(): string
    {
        return $this->message->id;
    }
}
