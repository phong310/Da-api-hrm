<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest as AdminLoginRequest;
use App\Http\Services\v1\Admin\AuthService;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    protected $authService;
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @param AdminLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AdminLoginRequest $request)
    {
        if (!empty($request->header('language'))) {
            $language = $request->header('language');
            App::setLocale($language);
        }
        $credentials = $request->all();
        $user = $this->authService->attempt($credentials);

        if (isset($user['errors'])) {
            return response()->json(['message' => $user['errors']], 408);
        }
        if (!$user) {
            return response()->json(['errors' => ['password' => trans('auth.password')]], 422);
        }
        $auth = $this->authService->accessToken($credentials);
        if ($user->company) {
            $user->setting = $user->company->setting;
        }
        $user->role = $user->getRoleNames()[0];

        // check status of company
        if ($user->role !== 'super_admin') {
            if ($user->company->status !== Company::STATUS['ACTIVE']) {
                return response()->json([
                    'message' => __('message.not_active'),
                ], 404);
            }
        }
        //

        $user->all_permissions = $user->getPermissionNamesByRole();
        $auth['user'] = $user;

        return response()->json($auth);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function me(Request $request)
    {
        $user = Auth::user();
        $user = $this->authService->show($user->id);
        if ($user->company) {
            $user->setting = $user->company->setting;
        }
        $user->role = $user->getRoleNames()[0];
        $user->all_permissions = $user->getPermissionNamesByRole();
        if ($user) {
            return response()->json($user);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        $refreshToken = $request->get('refresh_token');
        $auth = $this->authService->refreshToken($refreshToken);

        return response()->json($auth->data, $auth->code ?? 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->token()->revoke();

            return response()->json(['message' => 'Successful logout']);
        }

        return response()->json(['message' => 'Fail logout'], 401);
    }

    public function showUserNewCreateByCompanyId($company_id)
    {
        return $this->authService->showUserNewCreateByCompanyId($company_id);
    }
}
