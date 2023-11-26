<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleGroup;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class UpdatePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($company_id)
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::statement('SET foreign_key_checks = 0');

        $module_groups = Config::get('default_permissions.module_groups');
        $modules = $this->getModules(Config::get('default_permissions.modules'));
        $roles = Config::get('default_permissions.roles');
        $permissions = $this->getAllPermissions(Config::get('default_permissions.permissions'));

        //seed group modules
        foreach ($module_groups as $module_group) {
            ModuleGroup::query()->updateOrCreate(['name' => $module_group], ['name' => $module_group]);
        }

        //seed modules and module permissions
        foreach ($modules as $key => $module) {
            $moduleGroup = ModuleGroup::where('name', $module['group'])->first();
            $data = [
                'name' => $module['name'],
                'group_id' => $moduleGroup->id,
            ];
            Module::query()->updateOrCreate($data, $data);
        }

        //seed Permissions and module permissions
        foreach ($permissions as $permission) {
            $module = Module::query()->where(['name' => $permission['module']])->first();
            $data = [
                'name' => $permission['name'],
                'guard_name' => 'user-api',
                'module_id' => $module->id,
            ];
            Permission::query()->updateOrCreate($data, $data);
        }

        //seed roles then attach modules & permissions to roles
        foreach ($roles as $roleName => $role) {
            if ($roleName == 'super_admin') continue;

            $data = [
                'name' => $roleName,
                'guard_name' => $role['guard_name'],
                'company_id' => $company_id,
                'is_disabled' => true
            ];

            $newRole = Role::query()->updateOrCreate($data, $data);
            $permissions = $this->getPermissionsByRole($role['permissions']);
            $newRole->givePermissionTo($permissions);
        }

        DB::statement('SET foreign_key_checks = 1');
    }

    public function getPermissionsByRole($permissions)
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

    public function getAllPermissions($permissions)
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

    public function getModules($permissions)
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
