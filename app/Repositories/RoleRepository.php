<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use App\Repositories\Interfaces\RoleInterface;

class RoleRepository implements RoleInterface
{
    /**
     * @var User
     */
    protected $role;

    /**
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * @param $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function create($data)
    {
        return $this->role::query()->create($data);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function show($id)
    {
        return $this->role::query()->where(['id' => $id])->first();
    }

    /**
     * @param $data_filter
     * @param $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function updateOrCreate($data_filter, $data)
    {
        return $this->role::query()->updateOrCreate($data_filter, $data);
    }

    /**
     * @param $data
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function update($data, $id)
    {
        // TODO: Implement update() method.
        $role = $this->show($id);

        if (!$role) {
            return null;
        }

        $role->fill($data);
        $role->save();

        return $role;
    }

    /**
     * @param $name
     * @param $companyId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function showByName($name, $companyId)
    {
        return $this->role::query()->where(['company_id' => $companyId, 'name' => $name])->first();
    }
}
