<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AdminLoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class AuthController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);
        $token = Auth::guard('admin')->attempt($credentials);
        if (!$token) {
            return msg(trans('Invalid email or password'), ResponseAlias::HTTP_BAD_REQUEST);
        }
        $user = Auth::guard('admin')->user();
        $result['token'] = $token;
        $result['user_data'] = $user;
        return msgdata(trans('lang.code_sent'), $result, ResponseAlias::HTTP_OK);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Logs out the authenticated admin user
        auth('admin')->logout();

        // Return a success message with HTTP 200 OK status
        return msg(trans('lang.logout_s'), ResponseAlias::HTTP_OK);
    }


}

