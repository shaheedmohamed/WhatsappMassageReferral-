<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentWorkLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'work_hours',
        'messages_replied',
        'messages_auto_transferred',
        'messages_manual_transferred',
        'avg_response_time',
        'messages_by_group',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'messages_by_group' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
