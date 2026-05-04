<?php

namespace App\Http\Requests\Income;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class StoreIncomeRequest extends FormRequest
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


        $title             = ['nullable'];
        $delivery_man      = ['nullable'];
        $merchant          = ['nullable'];

        if ( Request::input('account_head_id')    == 1) :
            $merchant     = ['required'];
            $delivery_man = ['nullable'];

        elseif(Request::input('account_head_id')  == 2):
            $title        = ['nullable'];
            $merchant     = ['nullable'];
            $delivery_man = ['required'];
            // $hub_id       = ['required'];
        endif;
        if( Request::input('account_head_id')  == 3):
            $title        = ['required'];
            $delivery_man = ['nullable'];
            $merchant     = ['nullable'];
        endif;

        return [
            'account_head_id'   => ['required'],
            'title'             => $title,
            'merchant_id'       => $merchant ,
            'delivery_man_id'   => $delivery_man,
            'account_id'        => ['required'],
            'amount'            => ['required','numeric'],
            'date'              => ['required'],
            'receipt'           => 'nullable|image|mimes:jpeg,png,jpg|max:5098',
        ];
    }
}
