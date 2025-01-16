<?php

namespace App\Http\Requests;

use App\Models\Treasuries;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TreasuriesDeliveryRequest extends FormRequest
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
            'treasuries_can_delivery_id' => 'required',
        ];

    }

    public function messages(): array
    {
        return [
            'treasuries_can_delivery_id.required' => ' إسم الخزنة الرئيسية مطلوب',
        ];
    }    
}
