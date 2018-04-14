<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateProjectRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'category' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '项目名称不能为空',
            'category.required' => '项目类别至少选择一个'
        ];
    }

    function performCheck($type)
    {
        $positions = $this->dis_position;

        if($type == 1) {
            return in_array(1, $positions) && in_array(5, $positions);
        }else if($type == 2) {
            return in_array(2, $positions) && in_array(6, $positions);
        }else {
            return in_array(1, $positions) && in_array(2, $positions) && in_array(5, $positions) && in_array(6, $positions);
        }
    }
}
