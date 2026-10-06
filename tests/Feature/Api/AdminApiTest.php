<?php

namespace Tests\Feature\Api;

use App\Enums\AdminPermission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(array $permissions = null): User
    {
        return User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'admin_permissions' => $permissions,
        ]);
    }

    private function customerUser(): User
    {
        return User::factory()->create([
            'role' => UserRole::Customer,
            'is_active' => true,
        ]);
    }

    public function test_customer_cannot_access_admin_endpoints(): void
    {
        $user = $this->customerUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/dashboard/summary')
            ->assertForbidden();
    }

    public function test_unauthenticated_request_to_admin_endpoint_is_rejected(): void
    {
        $this->getJson('/api/v1/dashboard/summary')->assertUnauthorized();
    }

    public function test_admin_without_permission_cannot_access_restricted_endpoint(): void
    {
        // Admin with only kitchen permission, no dashboard
        $admin = $this->adminUser([AdminPermission::Kitchen->value]);
        $token = $admin->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/dashboard/summary')
            ->assertForbidden();
    }

    public function test_super_admin_can_access_dashboard(): void
    {
        // null permissions = super admin (all permissions)
        $admin = $this->adminUser(null);
        $token = $admin->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/dashboard/summary')
            ->assertOk()
            ->assertJsonStructure(['data' => ['stats']]);
    }

    public function test_admin_with_pos_permission_can_access_pos_bootstrap(): void
    {
        $admin = $this->adminUser([AdminPermission::Pos->value]);
        $token = $admin->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/pos/bootstrap')
            ->assertOk()
            ->assertJsonStructure(['data' => ['menu', 'tables', 'settings']]);
    }

    public function test_admin_with_orders_permission_cannot_access_pos_bootstrap(): void
    {
        $admin = $this->adminUser([AdminPermission::Orders->value]);
        $token = $admin->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/pos/bootstrap')
            ->assertForbidden();
    }

    public function test_admin_with_kitchen_permission_can_view_board(): void
    {
        $admin = $this->adminUser([AdminPermission::Kitchen->value]);
        $token = $admin->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/kitchen/board')
            ->assertOk()
            ->assertJsonStructure(['data' => ['orders']]);
    }

    public function test_admin_with_tables_permission_can_list_tables(): void
    {
        $admin = $this->adminUser([AdminPermission::Tables->value]);
        $token = $admin->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/tables')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_admin_orders_list_is_paginated(): void
    {
        $admin = $this->adminUser([AdminPermission::Orders->value]);
        $token = $admin->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/orders')
            ->assertOk()
            ->assertJsonStructure(['data', 'meta']);
    }
}
