<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserInterface;

class UserRepository implements UserInterface
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param $email
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function loginPageAdminByEmail($email)
    {
        return $this->user::query()->where('email', $email)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['super_admin', 'admin']);
            })
            ->first();
    }

    /**
     * @param $email
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function loginPageUserByEmail($email)
    {
        return $this->user::query()->where('email', $email)
            // ->whereHas('roles', function ($q) {
            //     $q->whereNotIn('name', ['super_admin']);
            // })
            ->with([
                'employee.personalInformation',
                'employee.personalInformation.country',
                'employee.personalInformation.job',
                'employee.position',
            ])
            ->first();
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function showUserPageAdmin($id)
    {
        return $this->user::query()->where('id', $id)
            ->first();
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function showUserPageUser($id)
    {
        return $this->user::query()
            ->where(['id' => $id])
            ->with([
                'employee.personalInformation',
                'employee.personalInformation.addresses',
                'employee.personalInformation.country',
                'employee.personalInformation.job',
                'employee.position',
            ])
            ->first();
    }
}
