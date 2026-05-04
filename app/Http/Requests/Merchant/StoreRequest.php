<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

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
        $kycRules = [
            'nid'      => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5098'],
            'nid_back' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5098'],
            'image_id' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5098'],
        ];

        return [
            'province_id'           => ['required'],
            'city_id'               => ['required'],
            'first_name'            => ['required'],
            'last_name'             => ['required'],
            'business_name'         => ['nullable', 'string', 'unique:merchants'],
            'mobile'                => ['required', 'numeric', 'digits_between:9,14', 'unique:users'],
            'email'                 => ['required', 'string', 'unique:users'],
            'status'                => ['required', 'numeric'],
            'password'              => ['required', 'min:6'],
            'location'              => ['nullable'],
            'business_address'      => ['nullable', 'string', 'max:191'],
            'payment_period'        => ['numeric'],
            'discount'              => ['numeric'],
        ] + $kycRules;
    }

    public function attributes()
    {
        return [
            'province_id'   => 'province',
            'city_id'       => 'city',
        ];
    }
}
