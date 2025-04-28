<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use BezhanSalleh\FilamentShield\Support\Utils;

class Role extends SpatieRole
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'guard_name',
        'description',
    ];

    /**
     * Default guard name for roles.
     */
    public static function defaultGuardName(): string
    {
        return Utils::getFilamentAuthGuard();
    }

    /**
     * Get the users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            config('permission.table_names.model_has_roles'),
            'role_id',
            config('permission.column_names.model_morph_key')
        );
    }

    /**
     * Get the permissions that belong to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            'role_id',
            'permission_id'
        );
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            $role->guard_name = $role->guard_name ?: static::defaultGuardName();
        });

        // Prevent deletion of admin role
        static::deleting(function ($role) {
            if ($role->name === 'admin') {
                throw new \Exception('The admin role cannot be deleted.');
            }
        });
    }
}
