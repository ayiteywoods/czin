<?php

namespace App\Models;

use App\Enums\AdminPermission;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'first_name', 'last_name', 'email', 'phone', 'password', 'role', 'is_active', 'admin_permissions', 'admin_notes'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'admin_permissions' => 'array',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->isAdmin() && $this->admin_permissions === null;
    }

    public function hasAdminPermission(AdminPermission|string $permission): bool
    {
        if (! $this->isAdmin()) {
            return false;
        }

        if ($this->admin_permissions === null) {
            return true;
        }

        $value = $permission instanceof AdminPermission ? $permission->value : $permission;

        return in_array($value, $this->admin_permissions, true);
    }

    /**
     * @return list<string>
     */
    public function assignedAdminPermissionValues(): array
    {
        if ($this->admin_permissions === null) {
            return array_map(
                fn (AdminPermission $permission) => $permission->value,
                AdminPermission::all(),
            );
        }

        return $this->admin_permissions;
    }

    public function defaultAdminRoute(): string
    {
        $routes = [
            AdminPermission::Dashboard->value => 'admin.dashboard',
            AdminPermission::Pos->value => 'admin.pos.index',
            AdminPermission::Kitchen->value => 'admin.kitchen.index',
            AdminPermission::Orders->value => 'admin.orders.index',
            AdminPermission::Tables->value => 'admin.tables.index',
            AdminPermission::Products->value => 'admin.products.index',
            AdminPermission::Categories->value => 'admin.categories.index',
            AdminPermission::Customers->value => 'admin.customers.index',
            AdminPermission::Content->value => 'admin.home-sections.index',
            AdminPermission::Users->value => 'admin.users.index',
            AdminPermission::Reports->value => 'admin.reports.index',
        ];

        foreach ($routes as $permission => $routeName) {
            if ($this->hasAdminPermission(AdminPermission::from($permission))) {
                return route($routeName);
            }
        }

        return route('home');
    }

    /**
     * Whether this admin may open an admin URL (used to avoid login 403s from a stale intended URL).
     */
    public function canAccessAdminUrl(?string $url): bool
    {
        if (! $this->isAdmin() || ! $this->is_active || ! filled($url)) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $path = '/'.ltrim($path, '/');

        if (! str_starts_with($path, '/admin')) {
            return true;
        }

        // Exact /admin landing is allowed for any admin with at least one area.
        if ($path === '/admin' || $path === '/admin/') {
            return $this->defaultAdminRoute() !== route('home');
        }

        $map = [
            '/admin/pos' => [AdminPermission::Pos, AdminPermission::Orders],
            '/admin/pos-report' => [AdminPermission::Pos, AdminPermission::Orders],
            '/admin/kitchen' => [AdminPermission::Kitchen],
            '/admin/orders' => [AdminPermission::Orders],
            '/admin/coupons' => [AdminPermission::Orders],
            '/admin/promotions' => [AdminPermission::Orders],
            '/admin/delivery' => [AdminPermission::Orders],
            '/admin/notifications' => [AdminPermission::Orders],
            '/admin/details/orders' => [AdminPermission::Orders],
            '/admin/products' => [AdminPermission::Products],
            '/admin/inventory' => [AdminPermission::Products],
            '/admin/modifiers' => [AdminPermission::Products],
            '/admin/recipes' => [AdminPermission::Products],
            '/admin/categories' => [AdminPermission::Categories],
            '/admin/details/categories' => [AdminPermission::Categories],
            '/admin/tables' => [AdminPermission::Tables],
            '/admin/reservations' => [AdminPermission::Tables],
            '/admin/customers' => [AdminPermission::Customers],
            '/admin/loyalty' => [AdminPermission::Customers],
            '/admin/details/customers' => [AdminPermission::Customers],
            '/admin/users' => [AdminPermission::Users],
            '/admin/locations' => [AdminPermission::Users],
            '/admin/staff-shifts' => [AdminPermission::Users],
            '/admin/homepage-sections' => [AdminPermission::Content],
            '/admin/testimonials' => [AdminPermission::Content],
            '/admin/pages' => [AdminPermission::Content],
            '/admin/email-templates' => [AdminPermission::Content],
            '/admin/store-settings' => [AdminPermission::Content],
            '/admin/shipping-regions' => [AdminPermission::Content],
            '/admin/maintenance-mode' => [AdminPermission::Content],
            '/admin/reports' => [AdminPermission::Reports],
            '/admin/analytics' => [AdminPermission::Reports],
            '/admin/dashboard' => [AdminPermission::Dashboard],
        ];

        // Longer prefixes first so /admin/pos-report wins over /admin/pos.
        uksort($map, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        foreach ($map as $prefix => $permissions) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                foreach ($permissions as $permission) {
                    if ($this->hasAdminPermission($permission)) {
                        return true;
                    }
                }

                return false;
            }
        }

        // Unknown admin paths: only super admins.
        return $this->isSuperAdmin();
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    public function loyaltyAccount(): HasOne
    {
        return $this->hasOne(LoyaltyAccount::class);
    }

    public function customerTags(): HasMany
    {
        return $this->hasMany(CustomerTag::class);
    }

    public function staffShifts(): HasMany
    {
        return $this->hasMany(StaffShift::class);
    }

    public function deliveryAssignments(): HasMany
    {
        return $this->hasMany(DeliveryAssignment::class, 'driver_user_id');
    }

    /**
     * @return array{token: string, abilities: list<string>}
     */
    public function apiProfilePayload(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role->value,
            'is_admin' => $this->isAdmin(),
            'permissions' => $this->isAdmin() ? $this->assignedAdminPermissionValues() : [],
        ];
    }
}
