<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'assigned_devices',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    public function whatsappDevices()
    {
        return $this->hasMany(WhatsappDevice::class);
    }

    public function workLogs()
    {
        return $this->hasMany(AgentWorkLog::class);
    }

    public function chatAssignments()
    {
        return $this->hasMany(ChatAssignment::class);
    }

    public function assignedMessages()
    {
        return $this->hasMany(WhatsappMessage::class, 'assigned_user_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function hasPermission($permission)
    {
        if ($this->isAdmin()) {
            return true;
        }
        
        return in_array($permission, $this->permissions ?? []);
    }

    public function updateActivity()
    {
        $this->update([
            'last_activity_at' => now(),
            'status' => 'online'
        ]);
    }
}
