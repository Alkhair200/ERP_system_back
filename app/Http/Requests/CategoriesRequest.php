<?php

namespace App\Http\Requests;

use App\Models\Categories;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CategoriesRequest extends FormRequest
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
            $data = Categories::where(['com_code' => $com_code ,'name' => $this->input('name')])->first();

        return [
            'name'  => $data != null ? 'required|min:3|unique:categories,name' : 'required|min:3',
        ];

    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name.unique' => 'الاسم مسجل من قبل',
            'name.min' => 'الإسم يجب ان يكون علي الاقل 3 احرف',
        ];
    }    
}
