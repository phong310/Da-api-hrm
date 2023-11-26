<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\User;
use App\Notifications\CreateCompanyNotification;
use App\Repositories\Interfaces\RoleInterface;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class UserService extends BaseService
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
     * @param $accounts
     * @param $companyId
     */
    public function storeMulti($accounts, $companyId)
    {
        foreach ($accounts as $item) {
            $data = [
                'user_name' => $item['user_name'],
                'password' => bcrypt($item['password']),
                'name' => $item['name'],
                'email' => $item['email'],
                'company_id' => $companyId,
            ];
            $user = User::query()->create($data);
            $role = $this->role->showByName($item['role'], $companyId);
            $user->assignRole($role->id);
        }
    }

    public function store($account, $companyId)
    {
        $data = [
            'user_name' => $account['user_name'],
            'password' => bcrypt($account['password']),
            'email' => $account['email'],
            'company_id' => $companyId,
        ];
        $user = User::query()->create($data);

        // if ($user) {
        //     $user->notify(new CreateCompanyNotification());
        // }

        $role = $this->role->showByName($account['role'], $companyId);
        $user->assignRole($role->id);
    }

    public function update($account, $companyId, $userId)
    {
        $data = [
            'user_name' => $account['user_name'],
            'password' => bcrypt($account['password']),
            'email' => $account['email'],
            'company_id' => $companyId,
        ];

        $user = $this->query->where(['id' => $userId, 'company_id' => $companyId])->first();
        $isChangeEmail = $user->email != $account['email'];
        $user->update($data);

        // if ($isChangeEmail) {
        //     $user->notify(new CreateCompanyNotification());
        // }
    }

    public function getByCompanyId($companyId)
    {
        return $this->query->where('company_id', $companyId)->get();
    }
}
