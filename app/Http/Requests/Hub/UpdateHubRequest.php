<?php

namespace App\Http\Requests\Hub;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class UpdateHubRequest extends FormRequest
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
        return [
            'name'                      => ['required', 'string', 'max:191', 'unique:hubs,name,' . Request::input('id')],
            'province_id'               => ['required', 'numeric'],
            'hub_type'                  => ['required', 'numeric'],
            'city_id'                   => ['required', 'numeric'],
            'postal_code'               => ['required', 'numeric'],
            'town_street_address'       => ['required'],
            'phone'                     => ['required', 'numeric', 'digits_between:9,14'],
            'building_address'          => ['required'],
            'email'                     => ['required'],
            'location'                  => ['required'],
        ];
    }

    public function  attributes()
    {
        return [
            'province_id'   => 'province',
            'city_id'       => 'city',
            'phone'         => 'phone number',
            'location'      => 'GPS location',
            'email'         => 'email address',
        ];
    }
}
