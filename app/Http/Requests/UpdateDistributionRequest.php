<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateDistributionRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'max_num' => 'required',
            'type' => 'required',
            'dis_position' => 'required',
            'dis_position.*' => 'required',
            'dis_member' => 'required',
            'dis_member.*' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'max_num.required' => '提交数据有误',
            'type.required' => '提交数据有误',
            'dis_position.required' => '分配信息不能为空',
            'dis_position.*.required' => '项目分工不能有空值',
            'dis_member.required' => '分配信息不能为空',
            'dis_member.*.required' => '部门人员不能有空值'
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
