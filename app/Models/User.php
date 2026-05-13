<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'tenant';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Role constants
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    /**
     * Get tasks for this user
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Check if user is tenant admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Scope: active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
