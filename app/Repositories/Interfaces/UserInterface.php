<?php

namespace App\Repositories\Interfaces;

interface UserInterface extends BaseInterface
{
    /**
     * @param $email
     * @return mixed
     */
    public function loginPageAdminByEmail($email);

    /**
     * @param $email
     * @return mixed
     */
    public function loginPageUserByEmail($email);

    /**
     * @param $id
     * @return mixed
     */
    public function showUserPageAdmin($id);

    /**
     * @param $id
     * @return mixed
     */
    public function showUserPageUser($id);

    public function showUserNewCreateByCompanyId($company_id);
}
