<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Role;
use App\Repositories\Interfaces\RoleInterface;
use App\Transformers\RoleTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class RoleService extends BaseService
{
    protected $role;

    /**
     * Instantiate a new controller instance.
     *
     * @param RoleInterface $role
     */
    public function __construct(RoleInterface $role)
    {
        $this->role = $role;
        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Role();
    }

    public function storeRoleDefault($company_id)
    {
        $default_roles = Config::get('default_permissions.roles_default');
        $roles = Config::get('default_permissions.roles');
        foreach ($default_roles as $item) {
            $data = [
                'name' => $item,
                'guard_name' => 'user-api',
                'company_id' => $company_id,
            ];
            $newRole = $this->role->updateOrCreate($data, $data);

            $permissions = $this->getPermissionsByRole($roles[$item]['permissions']);
            $newRole->givePermissionTo($permissions);
        }
    }

    /**
     * @param $permissions
     * @return array
     */
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

    /**
     * @param Request $request
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $message = '')
    {
        $data = [
            'name' => $request->name,
            'company_id' => $this->getCompanyId(),
            'guard_name' => 'user-api',
        ];
        $permissions = $request->permissions;
        try {
            DB::beginTransaction();
            $role = $this->role->create($data);
            $role->syncPermissions($permissions);
            DB::commit();

            return response()->json([
                'message' => __('message.created_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->errorResponse();
        }
    }
}
