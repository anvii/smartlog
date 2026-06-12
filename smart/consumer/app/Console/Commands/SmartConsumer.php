<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Junges\Kafka\Facades\Kafka;

class SmartConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smart:consume {topic}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!in_array($this->getTopic(), ['normal', 'high'])) {
            $this->error('The {topic} option is required (normal|high)');
            return;
        }

        $consumer = Kafka::consumer([$this->getTopic()])
            ->withHandler(function ($message) {
                $this->processMessage($message->getBody());
            })
            //->withMaxMessages(100)
            ->build();

        $consumer->consume();
    }

    /**
     * Process the message. Заглушка.
     * 
     * @param array $message Unserialized message model
     */
    protected function processMessage(array $message)
    {
        // Check if message was not sent early
        if (Redis::get($this->redisKey($message))) {
            $this->reportStatus($message, 'delivered', 'Duplicated message');
            Log::log('warning', 'Duplicated message ' . $message['id']);
            return;
        }

        // Consumer received the message - report status `sent`
        $this->reportStatus($message, 'sent');
        Log::log('info', 'Processed message ' . $message['id']);

        // Wait response from provider
        sleep(10);

        // Emulate delivery error
        if (mb_strpos($message['body'], 'test:error') !== false) {
            $this->reportStatus($message, 'error', 'Test: message rejected');
        }
        else {
            $this->reportStatus($message, 'delivered');
            Redis::set($this->redisKey($message), 'delivered');
        }
    }

    public function getTopic()
    {
        return $this->argument('topic');
    }

    protected function redisKey($message)
    {
        return 'message:id:' . $message['id'];
    }

    /**
     * Report message status to producer
     */
    protected function reportStatus(array $message, string $status, string $error=null)
    {
        $client = new Client();
        $url = env('PRODUCER_URL', 'http://smart_producer_1');
        $options = [
            'form_params' => [
                'message_id' => $message['id'],
                'status' => $status,
                'error' => $error,
            ],
        ];
        $client->post($url . '/api/message/status', $options);
    }
}
