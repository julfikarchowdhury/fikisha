<?php

namespace App\Http\Requests\MerchantPanel\Parcel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
        $from_point_type = Request::input('from_point_type');
        $to_point_type = Request::input('to_point_type');
        if ($from_point_type == 1 && $to_point_type == 1) {
            //door_to_door
            $from_city_id           = ['required'];
            $from_portal_code       = ['required'];

            $to_city_id             = ['required', 'numeric'];
            $to_portal_code         = ['required', 'numeric'];
        } else if ($from_point_type == 1 && $to_point_type == 2) {
            //door_to_hub
            $from_city_id           = ['required', 'numeric'];
            $from_portal_code       = ['required', 'numeric'];

            $to_city_id             = ['nullable', 'numeric'];
            $to_portal_code         = ['nullable', 'numeric'];
        } else if ($from_point_type == 2 && $to_point_type == 1) {
            //hub_to_door
            $from_city_id           = ['nullable', 'numeric'];
            $from_portal_code       = ['nullable', 'numeric'];

            $to_city_id             = ['required', 'numeric'];
            $to_portal_code         = ['required', 'numeric'];
        } else if ($from_point_type == 2 && $to_point_type == 2) {
            //hub_to_hub
            $from_city_id           = ['nullable', 'numeric'];
            $from_portal_code       = ['nullable', 'numeric'];

            $to_city_id             = ['nullable', 'numeric'];
            $to_portal_code         = ['nullable', 'numeric'];
        }

        return [
            'from_state_id'                                     => ['required'],
            'from_city_id'                                      => $from_city_id,
            'from_portal_code'                                  => $from_portal_code,
            'to_state_id'                                       => ['required'],
            'to_city_id'                                        => $to_city_id,
            'to_portal_code'                                    => $to_portal_code,

            'merchant_id'                                       => ['required', 'numeric'],
            'first_name'                                        => ['required'],
            'last_name'                                         => ['required'],
            'pickup_phone'                                      => ['required'],
            'pickup_address'                                    => ['required'],

            'customer_first_name'                               => ['required'],
            'customer_last_name'                                => ['required'],
            'customer_phone'                                    => ['required'],
            'customer_address'                                  => ['required'],
            // Item
            'package_type_id'                                   => ['required'],
            'quantity'                                          => ['required'],
            'who_pays_either'                                   => ['required'],
            'parcel_value'                                      => ['nullable', 'numeric'],
            'parcel_file'                                       => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:2048'],
        ];
    }

    public function attribute()
    {
        return [
            'from_state_id'                         => 'From State',
            'to_state_id'                           => 'To State',
            'from_city_id'                          => 'From City',
            'to_city_id'                            => 'To City',
            'customer_id'                           => 'customer',
            'to_portal_code'                        => 'From Portal code',
            'from_portal_code'                      => 'To Portal code',
            'policy'                                => 'Privacy Policy',
            'parcel_value'                          => 'Parcel Value Amount',
            'parcel_file'                           => 'Attach File/Document'
        ];
    }
 
}
