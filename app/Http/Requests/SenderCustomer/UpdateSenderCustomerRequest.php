<?php

namespace App\Http\Requests\SenderCustomer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateSenderCustomerRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'province_id'   => ['required', 'numeric'],
            'city_id'       => ['required', 'numeric'],
            'account_type'  => ['required', 'numeric'],
            'first_name'    => ['required', 'string'],
            'last_name'     => ['required', 'string'],
            'phone_number'  => ['required', 'string'],
            'address'       => ['required', 'string'],
            'status'        => ['required', 'numeric'],
        ];
    }
    
    public function attributes()
    {
        return [
            'province_id'   => 'province',
            'city_id'       => 'city',
        ];
    }
}
