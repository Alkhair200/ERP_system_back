<?php

namespace App\Http\Requests;

use App\Models\InvItemCard;
use Illuminate\Foundation\Http\FormRequest;

class InvItemCardEditRequest extends FormRequest
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
        $image = null;
       if ( $this->input('image') != null) {
            $image = $this->input('image');
       }

       $com_code = Auth()->user()->com_code;
       $data = null;
           $data = InvItemCard::where('id','!=',$this->id)->where(['com_code' => $com_code ,'name' => $this->input('name')])->first();

        return [
            'barcode' => 'required',
            'name' =>  $data != null ? 'required|min:3|unique:inv_item_cards,name,'.$this->id : 'required|min:3',
            'category_id' => 'required',
            
            'item_type' => 'required',
            'uom_id' =>'required' ,
            'does_has_reta_unit' =>'required' ,
            'has_fixced_price' =>'required',

            // اسعار الوحدة الاب اساسي
            'price' => 'required',
            'nos_gomla_price' => 'required',
            'gomla_price' => 'required',
            'post_price' => 'required',
            'image' => $image != null ? 'mimes:png,jpg,jpeg|max:200' : '',
            
          
         
        ];
    }
}
