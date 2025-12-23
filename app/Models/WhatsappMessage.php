<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $fillable = [
        'device_id',
        'message_id',
        'from_number',
        'from_name',
        'to_number',
        'message_body',
        'message_type',
        'message_timestamp',
        'replied',
        'reply_message',
        'reply_timestamp',
        'forwarded_to_admin',
        'admin_notified',
        'assigned_user_id',
        'assigned_at',
        'status',
    ];

    protected $casts = [
        'message_timestamp' => 'datetime',
        'forwarded_at' => 'datetime',
        'replied_at' => 'datetime',
        'forwarded_to_admin' => 'boolean',
        'replied' => 'boolean',
        'raw_data' => 'array',
    ];

    public function scopeUnreplied($query)
    {
        return $query->where('replied', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('message_timestamp', 'desc');
    }

    public function scopeFromNumber($query, $number)
    {
        return $query->where('from_number', $number);
    }

    public function device()
    {
        return $this->belongsTo(WhatsappDevice::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
