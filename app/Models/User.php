<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    // Role constants
    const ROLE_USER = 0;
    const ROLE_ADMIN = 1;
    const ROLE_STAFF = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'password',
        'phone_number',
        'avatar',
        'status',
        'is_admin',
        'role',
        'verification_token',
        'token_expires_at',
        'is_verified', 
        'salary',
        'hire_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getRoleNameAttribute()
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_STAFF => 'Staff',
            self::ROLE_USER => 'User',
            default => 'User'
        };
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStaff()
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isUser()
    {
        return $this->role === self::ROLE_USER;
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Kiểm tra user có permission không thông qua role
     */
    public function hasPermission(string $permissionName): bool
    {
        // Map role integer sang role name
        $roleName = match($this->role) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_STAFF => 'Staff',
            default => null
        };

        if (!$roleName) {
            return false;
        }

        // Lấy role từ database
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            return false;
        }

        // Kiểm tra role có permission này không
        return $role->permissions()->where('name', $permissionName)->exists();
    }

    /**
     * Lấy tất cả permissions của user thông qua role
     */
    public function getPermissions()
    {
        $roleName = match($this->role) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_STAFF => 'Staff',
            default => null
        };

        if (!$roleName) {
            return collect([]);
        }

        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            return collect([]);
        }

        return $role->permissions;
    }

    // Dynamic RBAC relations (Phase 2)
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user', 'user_id', 'permission_id');
    }

    /**
     * Lấy thông tin hạng thành viên của user
     */
    public function userLoyalty()
    {
        return $this->hasOne(UserLoyalty::class);
    }

    /**
     * Lấy hạng thành viên hiện tại của user
     */
    public function getCurrentLoyaltyTier()
    {
        return $this->userLoyalty?->loyaltyTier;
    }

    /**
     * Lấy tổng chi tiêu của user
     */
    public function getTotalSpent()
    {
        return $this->userLoyalty?->total_spent ?? 0;
    }

    /**
     * Lấy tỷ lệ giảm giá của hạng thành viên hiện tại
     */
    public function getLoyaltyDiscountRate()
    {
        $tier = $this->getCurrentLoyaltyTier();
        return $tier ? (float) $tier->discount_rate : 0;
    }

    /**
     * Quan hệ với Addresses
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Lấy địa chỉ mặc định
     */
    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }
}
