<?php

namespace App\Http\Requests;

use App\Models\Emplyees;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmRequest extends FormRequest
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
        $data = Emplyees::where(['com_code' => $com_code ,'name' => $this->input('name')])->first();

        return [
            'name' => $data != null ? 'required|unique: emplyees,name|min:3|max:100' : 'required|min:3|max:100',
            'start_balance_status' => 'required',
            'start_balance' => 'required|min:0',
            'active' =>'required',
            "departement_id" => 'required',
            "job_id" => 'required',
            "phone" => 'required',
            "address" => 'required',
            "salary" => 'required',

            "do_has_shift" => 'required',
            "shift_type_id" => 'required_if:do_has_shift,1',
            "total_hours" => 'required_if:do_has_shift,0',

            "does_has_social_insurance" => 'required',
            "social_insurance_value" => 'required_if:does_has_social_insurance,1',
            "social_insurance_num" => 'required_if:does_has_social_insurance,1',

            "do_has_social_motivation" => 'required',
            "motivation_value" => 'required_if:do_has_social_motivation,1',
            
            "does_has_allowances" => 'required',
            "allowances_value" => 'required_if:does_has_allowances,1',
        ];
    }

    public function messages()
    {
       return[
        'shift_type_id.required_if' => 'نوع شفت الموظف مطلوب',
        'total_hours.required_if' => 'عدد ساعات شفت الموظف مطلوب',
        'social_insurance_value.required_if' => 'قيمة التأمين الاجتماعي مطلوب',
        'motivation_value.required_if' => 'قيمة الحافز الشهري مطلوب',
        'allowances_value.required_if' => 'قيمة البدلات الشهرية مطلوب',
       ];
    }
}
