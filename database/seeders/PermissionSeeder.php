<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    private $permissions = [
        'dashboard'                   => [
            'menu',
        ],

        'head-of-family'              => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],

        'family-member'               => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],

        'social-assistance'           => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],

        'social-assistance-recipient' => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],

        'event'                       => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],

        'event-participant'           => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],

        'job-vacancy'                 => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],

        'job-applicant'               => [
            'menu',
            'create',
            'list',
            'edit',
            'delete',
        ],

        'profile'                     => [
            'menu',
            'create',
            'edit',
        ],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $key => $value) {
            foreach ($value as $permission) {
                Permission::firstOrCreate([
                    'name'       => $key . '-' . $permission,
                    'guard_name' => 'sanctum',

                ]);
            }
        }
    }
}
