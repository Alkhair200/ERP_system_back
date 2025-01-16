<?php

namespace App\Http\Requests;

use App\Models\Treasuries;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TreasuriesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $com_code = Auth()->user()->com_code;
        $data = null;
        if ($this->input('is_master') == 1) {
            $data = Treasuries::where(['com_code' => $com_code ,'is_master' => 1])->first();
        }

        return [
            'name' => 'required|min:3|unique:treasuries,name',
            'last_isal_collect' => 'required|integer|min:0',
            'last_isal_exchange' => 'required|integer|min:0',
            'is_master'  => $data != null ? 'required|unique:treasuries,is_master' : 'required',
            // يجب وجود نوع خزنه رئيسية واحده فقط
        ];

    }

    public function messages(): array
    {
        return [
            'name.required' => ' الاسم مطلوب',
            'name.unique' => 'الإسم مستخدم من قبل',
            'name.min' => 'الإسم يجب ان يكون علي الاقل 3 احرف',
            'is_master.required' => 'نوع الخزنة مطلوب',
            'is_master.unique' => 'لا يمكن ان يكون هناك أكثر من خزنة رئيسية',
            'last_isal_collect.min:0' => 'يجب إدخال رقم علي الاقل',
            'last_isal_exchange.min:0' => 'يجب إدخال رقم علي الاقل',
            'last_isal_collect.required' => 'اَخر إيصال تحصيل مطلوب',
            'last_isal_exchange.required' => 'اَخر إيصال صرف مطلوب',
        ];
    }    
}
