<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateRoleRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'alpha_dash|unique:roles,name,'.$this->id
        ];
    }

    public function messages()
    {
        return [
            'name.alpha_dash' => '角色简称仅能包含字母、数字、破折号以及下划线',
            'name.unique' => '角色简称已存在'
        ];
    }
}
