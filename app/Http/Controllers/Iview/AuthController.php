<?php

namespace App\Http\Controllers\Iview;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:iview', ['except' => ['login']]);
    }
    //
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('iview')->attempt($credentials)) {
            return $this->fail('帐号或者密码错误');
        }

        return $this->success('登录成功', [
            'access_token' => $token
        ]);
    }

    public function logout()
    {
        auth('iview')->logout();
        return $this->success('成功退出');
    }
}
