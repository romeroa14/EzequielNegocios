<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'message_type',
        'attachments',
        'read_by'
    ];

    protected $casts = [
        'attachments' => 'array',
        'read_by' => 'array'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function readByUsers()
    {
        return $this->belongsToMany(User::class, null, null, null, 'read_by');
    }
} 