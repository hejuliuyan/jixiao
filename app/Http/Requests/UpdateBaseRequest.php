<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateBaseRequest extends Request
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
            'max_num' => 'required',
            'depute_name' => 'required',
            'depute_user' => 'required',
            'depute_phone' => 'required|phone'
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
            'name.required' => '项目名称不能为空',
            'max_num' => '提交数据有误',
            'depute_name.required' => '委托单位名称不能为空',
            'depute_user.required' => '委托联系人不能为空',
            'depute_phone.required' => '委托单位联系电话不能为空',
            'depute_phone.phone' => '委托联系电话格式不正确'
        ];
    }
}
