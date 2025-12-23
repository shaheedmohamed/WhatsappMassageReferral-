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
        'employee_name',
        'assigned_at',
        'claimed_at',
        'completed_at',
        'released_at',
        'status',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'claimed_at' => 'datetime',
        'completed_at' => 'datetime',
        'released_at' => 'datetime',
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

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeOnHold($query)
    {
        return $query->where('status', 'on_hold');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function isClaimedByEmployee($userId)
    {
        return $this->user_id == $userId && in_array($this->status, ['in_progress', 'on_hold']);
    }

    public function canBeClaimed()
    {
        return in_array($this->status, ['pending', null]) || $this->status === 'on_hold';
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'in_progress' => 'جارى التعامل',
            'on_hold' => 'فى الانتظار',
            'completed' => 'تم الانتهاء',
            'pending' => 'قيد الانتظار',
            default => 'غير محدد'
        };
    }
}
