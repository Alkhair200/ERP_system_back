<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuppliersWithOrdersApproveBursahseRequest extends FormRequest
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
        return [
            'total_cost_items' => 'required',
            'tax_value' => 'required|max:100',

            'total_befor_discount' => 'required',
            'discount_value' => 'required',
            'total_cost' => 'required',
            'total_cost_items' => 'required',
            'total_cost_items' => 'required',
        ];
    }
}
