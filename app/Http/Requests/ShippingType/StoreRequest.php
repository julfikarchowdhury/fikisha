<?php

namespace App\Http\Requests\ShippingType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreRequest extends FormRequest
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
        $basic_price = ['required','numeric'];


        if(Request::input('delivery_type_id') == 3): 
            $basic_price = '';
        endif;
        return [
            'delivery_type_id'       => ['required','numeric'],
            'title'                  => ['required'],
            'basic_price'            => $basic_price,
            'start_weight'           => ['required','numeric'],
            'end_weight'             => ['required','numeric'],
            'addi_weight_price'      => ['required','numeric'],
            'start_volume'           => ['required','numeric'],
            'end_volume'             => ['required','numeric'],
            'addi_volume_price'      => ['required','numeric'], 
            'slots'                  => ['required'], 
        ];
    }
}
