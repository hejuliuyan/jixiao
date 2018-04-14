<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class StoreUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'email|unique:users',
            'password' => 'required|confirmed|min:6',
            'division' => 'required'
        ];
    }

    /**
     * 获取已定义验证规则的错误消息。
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '用户名不能为空',
            'email.email'  => '邮箱格式不正确',
            'email.unique' => '邮箱地址已被使用',
            'password.required'  => '密码不能为空',
            'password.confirmed'  => '两次密码不一致',
            'password.min'  => '密码不能小于6位',
            'division.required'  => '部门不能为空'
        ];
    }
}
