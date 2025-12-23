<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'super_admin_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function superAdmin()
    {
        return $this->belongsTo(User::class, 'super_admin_id');
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'community_id')->where('role', 'employee');
    }

    public function devices()
    {
        return $this->belongsToMany(WhatsappDevice::class, 'community_devices', 'community_id', 'device_id')
            ->withTimestamps();
    }
}
