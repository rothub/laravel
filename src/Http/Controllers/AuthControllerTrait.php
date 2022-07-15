<?php

namespace RotHub\Laravel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RotHub\Laravel\Exceptions\Exception;
use RotHub\Laravel\Models\Shorten;
use RotHub\Laravel\Rules\Rule;

trait AuthControllerTrait
{
    public function login(Request $request)
    {
        $input = $request->validate([
            'username' => Rule::username(),
            'password' => Rule::password(),
        ]);

        $auth = $this->auth();

        $token = $auth->attempt($input);
        $token or Exception::fail('账号或密码错误.');

        return Shorten::respondWithToken($auth->user(), $token);
    }

    public function refresh(Request $request)
    {
        $input = $request->validate(['refresh_token' => Rule::string(32, true)]);

        $auth = $this->auth();
        $oldToken = $auth->getToken();
        $newToken = $auth->refresh();
        $user = $auth->user();

        $refresh = Shorten::refreshToken($user, $oldToken);
        if ($input['refresh_token'] !== $refresh) {
            Exception::fail('refresh_token 错误.');
        }

        return Shorten::respondWithToken($user, $newToken);
    }

    public function logout()
    {
        $this->auth()->logout();
    }

    public function get()
    {
        return $this->auth()->user();
    }

    public function set(Request $request)
    {
        $input = $request->validate([
            'avatar' => Rule::url(),
            'nickname' => Rule::string(16, true),
            'sex' => Rule::string(16),
            'birthday' => Rule::date('Y-m-d'),
        ]);

        $this->update($input);
    }

    public function setPwd(Request $request)
    {
        $guard = 'admin';
        $input = $request->validate([
            'old_pwd' => 'password:' . $guard,
            'new_pwd' => Rule::password() . '|different:old_pwd',
        ]);

        $password = Hash::make($input['new_pwd']);
        $this->update(['password' => $password]);

        $this->auth()->logout();
    }

    public function resetPwd(Request $request)
    {
        $input = $request->validate([
            'password' => Rule::password(),
        ]);

        $password = Hash::make($input['password']);
        $this->update(['password' => $password]);

        $this->auth()->logout();
    }

    protected function update(array $input)
    {
        $user = $this->auth()->user();

        if ($user instanceof \Illuminate\Database\Eloquent\Model) {
            $user->updateOrFail($input);
        } else {
            Exception::fail('账号异常，请重新登录.');
        }
    }
}
