<?php

namespace App\Http\Requests\Vehicle;

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
            'name'                  => ['required', 'string'],
            'hub_id'                => ['required', 'string'],
            'registration_number'   => ['required', 'string', 'unique:vehicles'],
            'capacity'              => ['required', 'string'],
            'size'                  => ['required', 'string'],
            'status'                => ['required', 'numeric']
        ];
    }

    public function messages()
    {
        return [
            'hub_id.required' => 'The Hub field is required.',
        ];
    }
}
