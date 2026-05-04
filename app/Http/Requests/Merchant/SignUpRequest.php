<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\Request;

class SignUpRequest extends FormRequest
{
    use ApiReturnFormatTrait;
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
            'first_name'        => ['required', 'string', 'max:191'],
            'last_name'         => ['required', 'string', 'max:191'],
            'mobile'            => ['required', 'numeric', 'digits_between:9,14', 'unique:users'],
            'email'             => ['required', 'string', 'unique:users'],
            'business_name'     => ['nullable', 'string', 'unique:merchants'],
            'business_address'  => ['nullable', 'string', 'max:191'],
            'province_id'       => ['required'],
            'city_id'           => ['required'],
            'location'          => ['required'],
            'password'          => ['required', 'min:6'],
        ];
    }

    public function messages()
    {
        return [
            'province_id'           => "The Province field is required",
            'city_id'               => "The City field is required",
        ];
    }
 
}
