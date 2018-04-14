<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class StoreRoleRequest extends Request
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
            'name' => 'required|alpha|unique:roles',
            'display_name' => 'required',
            'description' => 'required',
            'permission' => 'required'
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
            'name.required' => '角色简称不能为空',
            'name.alpha' => '角色简称必须为字母',
            'name.unique' => '角色简称已存在',
            'display_name.required' => '角色名称不能为空',
            'description.required' => '角色说明不能为空',
            'permission.required' => '至少分配一个权限'
        ];
    }
}
