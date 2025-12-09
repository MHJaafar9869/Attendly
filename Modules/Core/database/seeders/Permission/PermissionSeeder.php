<?php

namespace Modules\Core\database\seeders\Permission;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $superAdminRole = Role::where('name', 'super_admin')->first();

        $settingsPermissions = [ // settings permissions
            ['name' => 'view_settings'],
            ['name' => 'create_settings'],
            ['name' => 'update_settings'],
            ['name' => 'delete_settings'],
            ['name' => 'restore_settings'],
            ['name' => 'force_delete_settings'],
        ];

        $studentsPermissions = [ // students permissions
            ['name' => 'view_students'],
            ['name' => 'create_students'],
            ['name' => 'update_students'],
            ['name' => 'delete_students'],
            ['name' => 'impersonate_students'],
        ];

        $teachersPermissions = [ // teachers permissions
            ['name' => 'view_teachers'],
            ['name' => 'create_teachers'],
            ['name' => 'update_teachers'],
            ['name' => 'delete_teachers'],
            ['name' => 'impersonate_teachers'],
            ['name' => 'assign_subjects'],
        ];

        $classroomsPermissions = [ // classrooms permissions
            ['name' => 'view_classrooms'],
            ['name' => 'create_classrooms'],
            ['name' => 'update_classrooms'],
            ['name' => 'delete_classrooms'],
        ];

        $departmentsPermissions = [ // departments permissions
            ['name' => 'view_departments'],
            ['name' => 'create_departments'],
            ['name' => 'update_departments'],
            ['name' => 'delete_departments'],
        ];

        $rolesPermissions = [ // roles & permission management permissions
            ['name' => 'view_roles'],
            ['name' => 'create_roles'],
            ['name' => 'update_roles'],
            ['name' => 'delete_roles'],
            ['name' => 'assign_roles_to_users'],
            ['name' => 'assign_permissions_to_roles'],
            ['name' => 'assign_permissions_to_users'],
        ];

        $permissionsPermissions = [ // permissions permissions
            ['name' => 'view_permissions'],
            ['name' => 'create_permissions'],
            ['name' => 'update_permissions'],
            ['name' => 'delete_permissions'],
        ];

        $statusesPermissions = [ // statuses permissions
            ['name' => 'view_statuses'],
            ['name' => 'create_statuses'],
            ['name' => 'update_statuses'],
            ['name' => 'delete_statuses'],
        ];

        $typesPermissions = [ // types permissions
            ['name' => 'view_types'],
            ['name' => 'create_types'],
            ['name' => 'update_types'],
            ['name' => 'delete_types'],
        ];

        $permissions = [
            ...$settingsPermissions,

            ...$studentsPermissions,

            ...$teachersPermissions,

            ...$classroomsPermissions,

            ...$departmentsPermissions,

            ...$rolesPermissions,

            ...$permissionsPermissions,

            ...$statusesPermissions,

            ...$typesPermissions,
        ];

        data_set($permissions, '*.created_at', $now);
        data_set($permissions, '*.updated_at', $now);

        DB::table('permissions')->upsert(
            $permissions,
            ['name'],
            ['name', 'updated_at']
        );

        $permissionIds = DB::table('permissions')->pluck('id')->toArray();

        if ($superAdminRole) {
            $superAdminRole->permissions()->syncWithoutDetaching($permissionIds);
        }
    }
}
