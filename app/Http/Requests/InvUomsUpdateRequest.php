<?php

namespace App\Http\Requests;

use App\Models\InvUoms;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InvUomsUpdateRequest extends FormRequest
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
            $data = InvUoms::where(['com_code' => $com_code ,'name' => $this->input('name')])->first();

        return [
            'name'  => $data != null ? 'required|unique:inv_uoms,name,'.$this->id  : 'required',
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
