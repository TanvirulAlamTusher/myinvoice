<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            /*
        |--------------------------------------------------------------------------
        | RESET CACHE
        |--------------------------------------------------------------------------
        */

        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // Dashboard
            'dashboard.view',
            'dashboard.sales.statistics.view',

              // Invoice
            'invoice.view',
            'invoice.statistics.view',
            'invoice.detail.view',
            'invoice.create',
            'invoice.edit',
            'invoice.delete',
            'invoice.print',
            'invoice.return',
            'invoice.cancel.return',
              // Customer
            'customer.view',
            'customer.statistics.view',
            'customer.detail.view',
            'customer.create',
            'customer.edit',
            'customer.delete',
              // supplier
            'supplier.view',
            'supplier.statistics.view',
            'supplier.detail.view',
            'supplier.create',
            'supplier.edit',
            'supplier.delete',
              // purchase return
            'purchase.return.view',
            'purchase.return.statistics.view',
            'purchase.return.detail.view',
            'purchase.return.create',
            'purchase.return.edit',
            'purchase.return.delete',

              // Product
            'product.view',
            'product.statistics.view',
            'product.detail.view',
            'product.stock.increment',
            'product.create',
            'product.edit',
            'product.delete',

                // category
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',
                // brand
            'brand.view',
            'brand.create',
            'brand.edit',
            'brand.delete',
                // unit
            'unit.view',
            'unit.create',
            'unit.edit',
            'unit.delete',



            // User
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',


            // Role permissions
            'role.permission.view',
            'role.permission.create',
            'role.permission.edit',
            'role.permission.delete',

            // Settings
            'settings.view',
            // business settings
            'business.settings.view',
            // backup restore
            'backup.restore.view',
            // trash
            'trash.view',
            'trash.restore',
            'trash.delete',
            'trash.delete.all',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'admin'
        ]);

        $manager = Role::firstOrCreate([
            'name' => 'manager'
        ]);

        $staff = Role::firstOrCreate([
            'name' => 'staff'
        ]);

        /*
        |--------------------------------------------------------------------------
        | ASSIGN PERMISSIONS
        |--------------------------------------------------------------------------
        */

        // Admin gets everything
        $admin->givePermissionTo(Permission::all());

        // Manager
        $manager->givePermissionTo([
            'dashboard.view',

            'product.view',
            'product.create',
            'product.edit',

            'customer.view',
            'customer.create',
            'customer.edit',

            'invoice.view',
            'invoice.create',
            'invoice.edit',
            'invoice.print',

        ]);

        // Staff
        $staff->givePermissionTo([
            'dashboard.view',

            'product.view',

            'customer.view',

            'invoice.view',
            'invoice.create',
            'invoice.print',
            'invoice.detail.view',
        ]);
    }
}
