<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleGroup;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DefaultPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::statement("SET foreign_key_checks = 0");

        $tables = ['module_groups', 'modules', 'roles', 'permissions', 'model_has_roles', 'model_has_permissions', 'role_has_permissions', 'role_has_modules'];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        $module_groups = Config::get('default_permissions.module_groups');
        $modules = $this->getModules(Config::get('default_permissions.modules'));
        $roles = Config::get('default_permissions.roles');
        $permissions = $this->getAllPermissions(Config::get('default_permissions.permissions'));

        //seed group modules
        foreach ($module_groups as $module_group) {
            ModuleGroup::create([
                'name' => $module_group
            ]);
        }

        //seed modules and module permissions
        foreach ($modules as $key => $module) {
            $moduleGroup = ModuleGroup::where('name', $module['group'])->first();
            Module::create([
                'name' => $module['name'],
                'group_id' => $moduleGroup->id
            ]);
        }

        //seed Permissions and module permissions
        foreach ($permissions as $permission) {
            $module = Module::query()->where(['name' => $permission['module']])->first();
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'user-api',
                'module_id' => $module->id
            ]);
        }

        //seed roles then attach modules & permissions to roles
        foreach ($roles as $roleName => $role) {
            $newRole = Role::create([
                'name' => $roleName,
                'guard_name' => $role['guard_name'],
                'company_id' => 1,
                'is_disabled' => true
            ]);
            $permissions = $this->getPermissionsByRole($role['permissions']);
            $newRole->givePermissionTo($permissions);
        }

        $superAdmin = User::query()->where(['email' => 'superadmin@hrm.com'])->first();
        $superAdmin->assignRole('super_admin');

        //assign roles to users
        $admins = User::query()->where(['email' => 'admin_epu@hrm.com'])->first();
        $admins->assignRole('admin');

        $user = User::query()->where(['email' => 'employee_epu@hrm.com'])->first();
        $user->assignRole('employee');

        DB::statement("SET foreign_key_checks = 1");
    }

    function getPermissionsByRole($permissions)
    {
        $data_permissions = [];
        foreach ($permissions as $module_name => $permission) {
            $sub_permissions = $permission['permissions'];
            foreach ($sub_permissions as $sub_permission => $p) {
                $name_permission = $module_name . '.' . $sub_permission;
                array_push($data_permissions, $name_permission);
            }
        }
        return $data_permissions;
    }

    function getAllPermissions($permissions)
    {
        $data_permissions = [];
        foreach ($permissions as $module_name => $permission) {
            foreach ($permission['permissions'] as $sub_permission => $p) {
                $name_permission = $module_name . '.' . $sub_permission;
                $data_permissions[] = [
                    'module' => $module_name,
                    'name' => $name_permission,
                ];
            }
        }
        return $data_permissions;
    }

    function getModules($permissions)
    {
        $data_modules = [];
        foreach ($permissions as $module_name => $permission) {
            $module_groups = $permission['module_groups'];
            $data_modules[] = [
                'name' => $module_name,
                'group' => $module_groups,
            ];
        }
        return $data_modules;
    }
}
