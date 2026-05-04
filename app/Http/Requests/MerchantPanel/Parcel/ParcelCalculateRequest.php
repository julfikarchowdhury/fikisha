<?php

namespace App\Http\Requests\MerchantPanel\Parcel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class ParcelCalculateRequest extends FormRequest
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
            'distance_km'                               => ['required', 'numeric'],
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
            'items.*.quantity.required'                         => 'Quantity is required',
        ];
    }
}
