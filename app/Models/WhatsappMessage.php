<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $fillable = [
        'message_id',
        'from_number',
        'from_name',
        'message_body',
        'message_type',
        'message_timestamp',
        'forwarded_to_admin',
        'forwarded_at',
        'replied',
        'reply_message',
        'replied_at',
        'raw_data',
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
}
