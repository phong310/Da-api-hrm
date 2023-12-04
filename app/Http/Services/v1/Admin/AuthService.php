<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\AuthBaseService;
use App\Repositories\Interfaces\UserInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService extends AuthBaseService
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @param $credentials
     * @return mixed
     */
    public function accessToken($credentials)
    {
        return $this->_accessToken($credentials);
    }

    /**
     * @param $credentials
     */
    public function attempt($credentials)
    {
        $email = $credentials['email'];
        $password = $credentials['password'];
        $user = $this->user->loginPageAdminByEmail($email);

        if (!$user || !Hash::check($password, $user->getAuthPassword())) {
            return null;
        }

        return $user;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->user->showUserPageAdmin($id);
    }

    /**
     * @param $user
     */
    public function updateLastLoginTime($user)
    {
        $user->update(['last_login_time' => now()]);
    }

    /**
     * @param $token
     * @return object
     */
    public function refreshToken($token)
    {
        return $this->_refreshToken($token);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getClient()
    {
        return DB::table('oauth_clients')
            ->where('provider', 'users')
            ->first();
    }

    public function showUserNewCreateByCompanyId($company_id)
    {
        $data =  $this->user->showUserNewCreateByCompanyId($company_id);
        return response()->json(['data' => $data]);
    }
}
