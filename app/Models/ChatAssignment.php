<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'chat_id',
        'chat_number',
        'assigned_at',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(WhatsappDevice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
