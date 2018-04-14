<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ExportExcelRequest extends Request
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
            'start_date' => 'required',
            'end_date' => 'required',
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
            'start_date.required' => '起始时间不能为空',
            'end_date.required' => '终止时间不能为空',
            'division.required' => '所属部门不能为空'
        ];
    }
}
