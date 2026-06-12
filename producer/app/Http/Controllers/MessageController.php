<?php

namespace App\Http\Controllers;

use App\Jobs\SendMessage;
use App\Models\Message;
use App\Models\MessageTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    /**
     * Display a listing of messages
     * 
     * http params (optional):
     * - client_id
     * - priority
     * - status
     * - channel
     */
    public function apiIndex(Request $request)
    {
        $query = Message::orderByDesc('created_at');
        if ($request->client_id) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->channel) {
            $query->where('channel', $request->channel);
        }
        return $query->get();
    }

    /**
     * Create a message
     * 
     * http params:
     * - client_id
     * - channel
     * - body
     * - priority
     */
    public function apiPost(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'array|required',
            'channel' => 'string|required',
            'body' => 'string|required',
            'priority' => 'string|required',
        ]);

        extract($data);

        if (!in_array($channel, ['email', 'sms'])) {
            throw new \Exception('Inavlid channel');
        }

        if (!in_array($priority, ['normal', 'high'])) {
            throw new \Exception('Inavlid priority');
        }

        foreach($client_id as $id)
        {
            // We cannot save message until it queued
            DB::transaction(function() use($id, $channel, $body, $priority) {
                $msg = Message::create([
                    'client_id' => $id,
                    'channel' => $channel,
                    'body' => $body,
                    'priority' => $priority,
                ]);

                $queue = $priority;
                SendMessage::dispatch($msg)->onQueue($queue);
            });
        }

        return [
            'status' => 'success',
        ];
    }

    /**
     * Update message status
     * 
     * Post params:
     * - message_id
     * - status
     * - error (optional)
     */
    public function apiStatus(Request $request)
    {
        $data = $request->validate([
            'message_id' => 'required|string',
            'status' => 'required|string',
            'error' => 'nullable|string',
        ]);

        extract($data);

        if (!in_array($status, ['created', 'queued', 'sent', 'delivered', 'error'])) {
            throw new \Exception('Invalid status');
        }

        $message = Message::findOrFail($message_id);
        $message->status = $status;
        if (isset($error)) {
            $message->error = $error;
        }
        $message->save();

        return [
            'status' => 'success',
        ];
    }

    /**
     * Get tracking messages
     * 
     * http params:
     * - message_id (optional)
     */
    public function apiTrack(Request $request)
    {
        $query = MessageTrack::orderByDesc('id');

        if ($request->message_id) {
            $query->where('message_id', $request->message_id);
        }

        return $query->get();
    }
}
