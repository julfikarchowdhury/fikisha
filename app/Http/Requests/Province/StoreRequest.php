<?php

namespace App\Http\Requests\Province;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => ['required'],
            'province_code' => ['required'],
            'position'      => ['numeric'],
        ];
    }

    public function attributes()
    {
        return [
            'province_code' => __('levels.province_code')
        ];
    }
}
