<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SubmitPaymentRequest extends Request
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
            'max_num'   => 'required',
            'subtotal'  => 'required|array',
//            'subtotal.*'=> 'required|numeric|regex:/^[1-9]\d*(\.\d+)?$/'
//            'subtotal.*'=> 'numeric|regex:/^[1-9]\d*(\.\d+)?$/'
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
            'max_num' => '提交数据有误',
            'subtotal.required' => '提交数据有误',
            'subtotal.array' => '提交数据有误',
//            'subtotal.*.required'=> '保存如有一项未收款，请输入0即可保存',
//            'subtotal.*.numeric'=> '金额小计必须为数字',
//            'subtotal.*.regex'=> '金额小计必须大于0'
        ];
    }
}
