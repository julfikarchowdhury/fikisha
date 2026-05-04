<?php

namespace App\Http\Requests\Town;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [ 
            'city_id'               => ['required'],
            'district_id'           => ['required'],
            'name'                  => ['required','required'],
            'portal_code'           => ['required'],
        ];
    }
}
