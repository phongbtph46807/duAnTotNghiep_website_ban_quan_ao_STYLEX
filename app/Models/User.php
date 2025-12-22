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
    const ROLE_WAREHOUSE_MANAGER = 3;

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
        'wallet_balance',
        'wallet_history',
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
            'wallet_history' => 'array'
        ];
    }
    public function getRoleNameAttribute()
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_STAFF => 'Staff',
            self::ROLE_WAREHOUSE_MANAGER => 'Warehouse Manager',
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

    public function isWarehouseManager()
    {
        return $this->role === self::ROLE_WAREHOUSE_MANAGER;
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
        // Kiểm tra permissions trực tiếp từ user (nếu có)
        if ($this->permissions()->where('name', $permissionName)->exists()) {
            return true;
        }

        // Kiểm tra permissions từ roles (many-to-many)
        // Load roles với permissions để tránh N+1 query
        $userRoles = $this->roles()->with('permissions')->get();
        foreach ($userRoles as $role) {
            // Kiểm tra trong collection đã load
            $hasPermission = $role->permissions->contains(function ($permission) use ($permissionName) {
                return $permission->name === $permissionName;
            });
            
            if ($hasPermission) {
                return true;
            }
            
            // Fallback: Kiểm tra bằng query nếu không tìm thấy trong collection
            if ($role->permissions()->where('name', $permissionName)->exists()) {
                return true;
            }
        }

        // Backward compatibility: Kiểm tra trường role cũ
        $roleName = match ($this->role) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_STAFF => 'Staff',
            self::ROLE_WAREHOUSE_MANAGER => 'Warehouse Manager',
            default => null
        };
if ($roleName) {
            $role = Role::where('name', $roleName)->with('permissions')->first();
            if ($role) {
                // Kiểm tra trong collection đã load
                $hasPermission = $role->permissions->contains(function ($permission) use ($permissionName) {
                    return $permission->name === $permissionName;
                });
                
                if ($hasPermission) {
                    return true;
                }
                
                // Fallback: Kiểm tra bằng query
                if ($role->permissions()->where('name', $permissionName)->exists()) {
                return true;
                }
            }
        }

        return false;
    }

    /**
     * Lấy tất cả permissions của user thông qua role
     */
    public function getPermissions()
    {
        // Ưu tiên lấy permissions từ RBAC roles (hỗ trợ role mới)
        $permissions = collect();
        
        // Lấy permissions từ tất cả roles của user (RBAC)
        $userRoles = $this->roles()->with('permissions')->get();
        foreach ($userRoles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }
        
        // Loại bỏ duplicate permissions
        $permissions = $permissions->unique('id');
        
        // Fallback: Nếu không có permissions từ RBAC, lấy từ role integer cũ
        if ($permissions->isEmpty()) {
        $roleName = match ($this->role) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_STAFF => 'Staff',
                self::ROLE_WAREHOUSE_MANAGER => 'Warehouse Manager',
            default => null
        };

            if ($roleName) {
        $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $permissions = $role->permissions;
                }
            }
        }
        
        return $permissions;
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

    /**
     * Quan hệ với Reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Quan hệ với Wishlist (many-to-many với Products)
     */
    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlist', 'user_id', 'product_id')->withTimestamps();
    }

    /**
     * Lấy URL avatar - hỗ trợ cả URL từ Google và file path trong storage
     */
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return \App\Http\Controllers\Admin\UserController::URLIMAGEDEFAULT;
        }

        // Nếu avatar là URL đầy đủ (từ Google hoặc external), dùng trực tiếp
        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        // Nếu là file path trong storage, dùng asset
        return asset('storage/' . $this->avatar);
    }
}