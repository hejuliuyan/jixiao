<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SubmitProjectRequest extends Request
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'depute_name' => 'required',
            'depute_user' => 'required',
            'depute_phone' => 'required|phone',
            'category' => 'required',
            'dis_position' => 'required',
            'dis_position.*' => 'required',
            'dis_member' => 'required',
            'dis_member.*' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '项目名称不能为空',
            'depute_name.required' => '委托单位名称不能为空',
            'depute_user.required' => '委托联系人不能为空',
            'depute_phone.required' => '委托单位联系电话不能为空',
            'depute_phone.phone' => '委托联系电话格式不正确',
            'category.required' => '项目类别不能为空',
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