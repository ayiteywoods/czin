<?php

namespace App\Console\Commands;

use App\Enums\AdminPermission;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class GrantPosStaffCommand extends Command
{
    protected $signature = 'admin:grant-pos-staff
                            {email : Admin user email}
                            {--only-show : Show current permissions without changing them}';

    protected $description = 'Grant Products, Orders, Point of Sale, and Reports to an admin user (POS staff preset).';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            $this->error("No user found for {$email}.");

            return self::FAILURE;
        }

        $this->line("User: {$user->name} <{$user->email}>");
        $this->line('Role: '.($user->role?->value ?? 'null'));
        $this->line('Active: '.($user->is_active ? 'yes' : 'no'));
        $this->line('Super admin: '.($user->isSuperAdmin() ? 'yes' : 'no'));
        $this->line('Permissions: '.json_encode($user->admin_permissions));

        if ($this->option('only-show')) {
            return self::SUCCESS;
        }

        if ($user->role !== UserRole::Admin) {
            $user->role = UserRole::Admin;
        }

        $user->is_active = true;
        $user->admin_permissions = AdminPermission::posStaffPreset();
        $user->save();

        $this->info('Updated permissions to POS staff preset: '.implode(', ', AdminPermission::posStaffPreset()));
        $this->line('Ask the user to log out and log in again.');

        return self::SUCCESS;
    }
}
