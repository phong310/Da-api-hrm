<?php

namespace App\Http\Controllers\Api\v1\User;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRegister;
use App\Http\Requests\UserLogin;
use App\Http\Services\v1\User\AuthService;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    // Register
    public function register(UserRegister $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = bcrypt($validatedData['password']);
        $user = User::create($validatedData);
        return response()->json(['user' => $user, 'msg' => "Register successfully"], 200);
    }

    // Login
    public function login(UserLogin $request)
    {
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

        $user->all_permissions = $user->getPermissionNamesByRole();
        $user->company = $user->company;
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
        if ($user) {
            $user = $this->authService->show($user->id);

            if ($user->company) {
                $user->setting = $user->company->setting;
            }

            $user->role = $user->getRoleNames()[0];
            $user->all_permissions = $user->getPermissionNamesByRole();

            return response()->json($user);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function info(Request $request)
    {
        $user = Auth::user();
        $request->user()->getPermissionsViaRoles();
        if ($user) {
            return response()->json($user->employee->personalInformation);
        }
    }

    // Refresh-token
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

    // Logout
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return response()->json(['message' => 'Successful logout'], 200);
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }
}
