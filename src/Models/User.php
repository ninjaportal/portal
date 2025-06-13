<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NinjaPortal\Admin\Models\UserApp;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    static string $ACTIVE_STATUS = 'active';
    static string $INACTIVE_STATUS = 'inactive';
    static string $DEFAULT_STATUS = 'pending';

    static array $USER_STATUS = [
        'active' => 'active',
        'inactive' => 'inactive',
        'pending' => 'pending'
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'status',
        'email_verified_at',
        'remember_token',
        'custom_attributes',
        'sync_with_apigee'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'custom_attributes' => 'array',
        'sync_with_apigee' => 'boolean'
    ];

    protected $appends = [
        'full_name'
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function audiences(): BelongsToMany
    {
        return $this->belongsToMany(Audience::class);
    }
}
