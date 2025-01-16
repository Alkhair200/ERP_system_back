<?php

namespace App\Http\Requests;

use App\Models\Sales_Matrial_types;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SalesMatrialRequest extends FormRequest
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
            $data = Sales_Matrial_types::where(['com_code' => $com_code ,'name' => $this->input('name')])->first();

        return [
            'name'  => $data != null ? 'required|unique:sales_matrial_types,name' : 'required',
        ];

    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name.unique' => 'الاسم مسجل من قبل',
        ];
    }    
}
