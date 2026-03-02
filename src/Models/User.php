<?php

namespace NinjaPortal\Portal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NinjaPortal\Portal\Query\Filters\UserFilter;
use NinjaPortal\Portal\Query\Search\UserSearch;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'status',
        'email_verified_at',
        'remember_token',
        'custom_attributes',
        'sync_with_apigee',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'custom_attributes' => 'array',
        'sync_with_apigee' => 'boolean',
    ];

    protected $appends = [
        'full_name',
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

    public function scopeSearch(Builder $builder): Builder
    {
        return (new UserSearch)->apply($builder);
    }

    public function scopeFilter(Builder $builder): Builder
    {
        return (new UserFilter)->apply($builder);
    }

    public static function statuses(): array
    {
        $statuses = config('ninjaportal.user.statuses', ['active', 'inactive', 'pending']);

        return array_values(array_filter(array_map(
            static fn (mixed $status): string => strtolower(trim((string) $status)),
            is_array($statuses) ? $statuses : []
        )));
    }

    public static function defaultStatus(): string
    {
        $configured = strtolower(trim((string) config('ninjaportal.user.default_status', 'pending')));
        $statuses = static::statuses();

        if ($configured !== '' && in_array($configured, $statuses, true)) {
            return $configured;
        }

        return $statuses[0] ?? 'pending';
    }

    public static function activeStatus(): string
    {
        return in_array('active', static::statuses(), true) ? 'active' : static::defaultStatus();
    }

    public static function inactiveStatus(): string
    {
        return in_array('inactive', static::statuses(), true) ? 'inactive' : static::defaultStatus();
    }
}
