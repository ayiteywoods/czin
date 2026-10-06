<?php

namespace App\Enums;

enum AdminPermission: string
{
    case Dashboard = 'dashboard';
    case Products = 'products';
    case Categories = 'categories';
    case Orders = 'orders';
    case Pos = 'pos';
    case Customers = 'customers';
    case Users = 'users';
    case Content = 'content';
    case Reports = 'reports';
    case Tables = 'tables';
    case Kitchen = 'kitchen';

    public function label(): string
    {
        return match ($this) {
            self::Dashboard => 'Dashboard',
            self::Products => 'Products',
            self::Categories => 'Categories',
            self::Orders => 'Orders',
            self::Pos => 'Point of Sale',
            self::Customers => 'Customers',
            self::Users => 'Admin users',
            self::Content => 'Website content',
            self::Reports => 'Reports',
            self::Tables => 'Tables',
            self::Kitchen => 'Kitchen',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Dashboard => 'View dashboard overview and analytics',
            self::Products => 'Create, edit, and remove products',
            self::Categories => 'Manage product categories',
            self::Orders => 'View and update orders, coupons, promotions, and delivery',
            self::Pos => 'Use the Point of Sale terminal and POS end-of-day report',
            self::Customers => 'View and manage customer accounts',
            self::Users => 'Create and manage admin users and permissions',
            self::Content => 'Edit homepage sections, store settings, testimonials, and legal pages',
            self::Reports => 'View and export sales reports',
            self::Tables => 'Manage dining tables, areas, and seating status',
            self::Kitchen => 'View the kitchen board and update food preparation status',
        };
    }

    /**
     * Suggested permissions for a front-of-house / POS staff account.
     *
     * @return list<string>
     */
    public static function posStaffPreset(): array
    {
        return [
            self::Products->value,
            self::Orders->value,
            self::Pos->value,
            self::Reports->value,
        ];
    }

    /**
     * @return list<self>
     */
    public static function all(): array
    {
        return self::cases();
    }
}
