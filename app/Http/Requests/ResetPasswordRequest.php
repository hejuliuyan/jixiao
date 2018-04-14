<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ResetPasswordRequest extends Request
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
            'old_password' => 'required|min:6',
            'password' => 'required|confirmed|min:6'
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
            'old_password.required' => '原密码不能为空',
            'old_password.min' => '原密码不能小于6位',
            'password.required'  => '新密码不能为空',
            'password.confirmed'  => '两次新密码不一致',
            'password.min'  => '新密码不能小于6位'
        ];
    }
}
