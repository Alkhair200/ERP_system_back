<?php

namespace App\Http\Requests;

use App\Models\InvUoms;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InvUomsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $com_code = Auth()->user()->com_code;
        $data = null;
        $data = InvUoms::where(['com_code' => $com_code ,'name' => $this->input('name')])->first();

        return [
            'name' => $data != null ? 'required|unique:inv_uoms,name' : 'required',
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
