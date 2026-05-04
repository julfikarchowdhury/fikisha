<?php

namespace App\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;

class StoreMultipleRequest extends FormRequest
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
            'province_id'       => 'required',
            'province_code'     => 'required',
            "name.*"            => "required|string",
            "portal_code.*"     => "required",
        ];
    }

    public function messages()
    {
        return [
            'province_id.required' => 'Please select province',
        ];
    }
}
