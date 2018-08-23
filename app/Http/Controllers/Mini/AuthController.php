<?php

namespace App\Http\Controllers\Mini;

use App\Models\User;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(string $code)
    {
        $app = app('wechat.mini_program');
        $res = $app->auth->session($code);
        if (isset($res['errcode'])) {
            return $this->fail('invalid code');
        }
        $user = User::where(['openid' => $res['openid']])->first();
        if (!$user) {
            return $this->fail('对不起，你没有权限');
        }
        $token = auth('mini')->claims(['session_key' => $res['session_key']])->login($user);
        return [
            'data' => [
                'token' => $token,
            ]
        ];
    }
}
