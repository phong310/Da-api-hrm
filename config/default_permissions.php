<?php

/**
 * Definition roles, modules & permissions.
 *
 * permission: [controllerName]_[actionName]. eg: user_index => UserController@index
 */
$roles_default = ['admin', 'manager', 'accountant', 'employee'];

$sub_permissions_base = [
    'list' => 'index',
    'store' => 'store',
    'update' => 'update',
    'detail' => 'detail',
    'destroy' => 'destroy',
];
$module_groups = [
    'SYSTEM' => 'system',
    'EMPLOYEE' => 'employee',
    'ACCOUNT' => 'account',
    'FORM' => 'form',
    'COMPANY' => 'company',
];
$all_permissions = [
    'users' => [
        'module_groups' => $module_groups['ACCOUNT'],
        'permissions' => $sub_permissions_base,
    ],
    'employees' => [
        'module_groups' => $module_groups['EMPLOYEE'],
        'permissions' => array_merge($sub_permissions_base, ['manage' => 'manage'])
    ],
    'timekeeping' => [
        'module_groups' => $module_groups['EMPLOYEE'],
        'permissions' => array_merge($sub_permissions_base, ['manage' => 'manage']),
    ],
    'timesheet-logs' => [
        'module_groups' => $module_groups['EMPLOYEE'],
        'permissions' => array_merge($sub_permissions_base, ['manage' => 'manage']),
    ],
    'labor-contracts' => [
        'module_groups' => $module_groups['EMPLOYEE'],
        'permissions' => array_merge($sub_permissions_base, ['manage' => 'manage']),
    ],
    'salaries' => [
        'module_groups' => $module_groups['EMPLOYEE'],
        'permissions' => array_merge($sub_permissions_base, ['manage' => 'manage']),
    ],
    'leave-form' => [
        'module_groups' => $module_groups['FORM'],
        'permissions' => array_merge($sub_permissions_base, ['manage' => 'manage']),
    ],
    'overtime' => [
        'module_groups' => $module_groups['FORM'],
        'permissions' => array_merge($sub_permissions_base, ['manage' => 'manage']),
    ],
    'request-change-timesheets' => [
        'module_groups' => $module_groups['FORM'],
        'permissions' => array_merge($sub_permissions_base, ['manage' => 'manage']),
    ],
    'compensatory_leave' => [
        'module_groups' => $module_groups['FORM'],
        'permissions' => array_merge($sub_permissions_base, ['manage' => 'manage']),
    ],
    'companies' => [
        'module_groups' => $module_groups['COMPANY'],
        'permissions' => $sub_permissions_base,
    ],
];

$permission_ignore_employee = ['employees.*', 'leave-form.manage', 'overtime.manage', 'request-change-timesheets.manage', 'compensatory_leave.manage', 'labor-contracts.manage', 'salaries.manage'];
$permission_ignore_admin = [];
$permission_ignore_accountant = [];
$permission_ignore_manager = ['employees.*', 'labor-contracts.manage', 'salaries.manage'];

$ignorePermissionByArrayOfKeys = function ($permissions, $arrayOfKeys) {
    foreach ($arrayOfKeys as $key) {
        $keyPrefix = explode('.', $key)[0];
        $keySuffix = explode('.', $key)[1];

        if ($keySuffix === "*") {
            foreach ($permissions as $module_name => $permission) {
                if ($module_name === $keyPrefix) {
                    unset($permissions[$module_name]);
                }
            }
        } else {
            foreach ($permissions as $module_name => $permission) {
                if ($module_name === $keyPrefix) {
                    foreach ($permission['permissions'] as $sub_permission_key => $sub_permission_value) {
                        if ($sub_permission_key === $keySuffix) {
                            unset($permissions[$module_name]['permissions'][$sub_permission_key]);
                        }
                    }
                }
            }
        }
    }
    return $permissions;
};

$admin_permissions = $ignorePermissionByArrayOfKeys($all_permissions, $permission_ignore_admin);
$accountant_permissions = $ignorePermissionByArrayOfKeys($all_permissions, $permission_ignore_accountant);
$employee_permissions = $ignorePermissionByArrayOfKeys($all_permissions, $permission_ignore_employee);
$manager_permissions = $ignorePermissionByArrayOfKeys($all_permissions, $permission_ignore_manager);


return [
    //module group
    'module_groups' => $module_groups,
    //permission
    'permissions' => $all_permissions,
    //roles_default
    'roles_default' => $roles_default,
    //modules
    'modules' => $all_permissions,
    //role has permission
    'roles' => [
        'super_admin' => [
            'guard_name' => 'user-api',
            'company_id' => null,
            'permissions' => $all_permissions,
        ],
        'admin' => [
            'guard_name' => 'user-api',
            //            'company_id' => 1,
            'permissions' => $admin_permissions,
        ],
        'manager' => [
            'guard_name' => 'user-api',
            //            'company_id' => 1,
            'permissions' => $manager_permissions,
        ],
        'accountant' => [
            'guard_name' => 'user-api',
            //            'company_id' => 1,
            'permissions' => $accountant_permissions,
        ],
        'employee' => [
            'guard_name' => 'user-api',
            //            'company_id' => 1,
            'permissions' => $employee_permissions,
        ]
    ],
];
