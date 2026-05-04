<?php

namespace App\Http\Requests\MerchantPanel\Parcel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class StoreApiRequest extends FormRequest
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
        $schedule_date = '';
        if (Request::input('pick_type') == 3) :
            $schedule_date = ['required'];
        endif;

        $customer_name = ['required', 'string', 'max:191'];
        $customer_phone = ['required', 'string', 'max:191'];

        return [
            'from_province'                             => ['required'],
            'to_province'                               => ['required'],
            'from_city'                                 => ['required'],
            'to_city'                                   => ['required'],
            'from_portal_code'                          => ['required', 'numeric'],
            'to_portal_code'                            => ['required', 'numeric'],

            'pickup_location'                           => ['required'],
            'pickup_lat'                                => ['required'], 
            'drop_location'                             => ['required'],
            'drop_latitude'                             => ['required'],
            'drop_longitude'                            => ['required'],

            'receiver_name'                             => $customer_name,
            'receiver_phone'                            => $customer_phone,
            'from_whatsapp_number'                      => ['numeric'],
            'to_whatsapp_number'                        => ['numeric'],

            'pickup_type'                               => 'required',
            'schedule_date'                             => $schedule_date,
            'items.*.quantity'                          => 'required',
            'items.*.item_category_id'                  => 'nullable',
            'items.*.item_value'                        => 'nullable',
            'items.*.item_unit_parcel_service_cost'     => 'nullable',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'from_province.required'                            => 'from state is required',
            'to_province.required'                              => 'to state is required',
            'from_city.required'                                => 'from city is required',
            'to_city.required'                                  => 'to city is required',
            'items.*.quantity.required'                         => 'Quantity is required',
        ];
    }
}
