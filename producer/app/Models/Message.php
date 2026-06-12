<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Override;

class Message extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $guarded = [];

    #[Override]
    protected static function boot()
    {
        parent::boot();

        static::creating(function(Message $message) {
            $message->id = md5(implode(':', [$message->client_id, $message->body]));
            $message->status = 'created';
            MessageTrack::create([
                'message_id' => $message->id,
                'status' => $message->status,
            ]);
        });

        static::updating(function(Message $message) {
            if ($message->isDirty('status')) {
                MessageTrack::create([
                    'message_id' => $message->id,
                    'status' => $message->status,
                ]);
            }
        });
    }
}
