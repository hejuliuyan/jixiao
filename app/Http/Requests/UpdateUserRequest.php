<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateUserRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'email|unique:users,email,' . $this->id,
            'password' => 'confirmed|min:6'
        ];
    }

    public function messages()
    {
        return [
            'email.email' => '邮箱格式不正确',
            'email.unique' => '邮箱地址已被使用',
            'password.confirmed' => '两次密码不一致',
            'password.min' => '密码不能小于6位'
        ];
    }
}
