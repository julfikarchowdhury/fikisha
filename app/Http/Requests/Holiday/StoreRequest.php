<?php

namespace App\Http\Requests\Holiday;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            'name'     => ['required'],
            'from_date'=> ['required','before_or_equal:to_date'],
            'to_date'  => ['required'],
        ];
    }

    public function attributes(){
        return [
            'from_date' => __('parcel.from_date'),
            'to_date'   => __('parcel.to_date')
        ];
    }
}
