<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'is_active',
        'department',
        'phone',
        'created_by',
        'updated_by',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    // ─── Role Helpers ──────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManagement(): bool
    {
        return $this->role === 'management';
    }

    public function isNormal(): bool
    {
        return $this->role === 'normal';
    }

    /**
     * Admin and Management have access to everything.
     * Normal users need an explicit permission record.
     */
    public function canAccessModule(string $moduleSlug, string $action = 'can_view'): bool
    {
        if ($this->isAdmin() || $this->isManagement()) {
            return true;
        }

        return $this->modulePermissions()
            ->whereHas('module', fn($q) => $q->where('slug', $moduleSlug)->where('is_active', true))
            ->where($action, true)
            ->exists();
    }

    // ─── Relationships ─────────────────────────────────────────────

    public function modulePermissions()
    {
        return $this->hasMany(UserModulePermission::class);
    }

    public function permittedModules()
    {
        return $this->hasManyThrough(
            Module::class,
            UserModulePermission::class,
            'user_id',
            'id',
            'id',
            'module_id'
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
