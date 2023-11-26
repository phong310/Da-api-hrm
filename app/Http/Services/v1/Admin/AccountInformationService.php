<?php

namespace App\Http\Services\v1\Admin;

use App\Models\User;
use App\Repositories\Interfaces\RoleInterface;

class AccountInformationService extends BaseMasterService
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
        $this->model = new User();
    }

    /**
     * @param $employee_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByEmployee($employee_id)
    {
        $result = $this->query->where(['employee_id' => $employee_id])->first();
        $result->role = $result->getRoleNames()[0];

        return $result;
    }

    /**
     * @param $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function createByEmployee($request)
    {
        $data = $request->all();

        try {
            $user = User::create($data);

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'server_error'], 500);
        }
    }


    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateByEmployee($request, $id)
    {
        $data = $request->all();
        $companyId = $this->getCompanyId();
        $user = $this->query->where(['id' => $id])->first();

        try {
            $user = $this->query->where(['id' => $id])->first();

            $user->update(collect($data)
                ->only((new User())->getFillable())->all());
            $role = $this->role->showByName($request->role, $companyId);
            $user->syncRoles($role->id);

            return response()->json([
                'message' => __('message.update_success'),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'server_error'], 500);
        }
    }
}
