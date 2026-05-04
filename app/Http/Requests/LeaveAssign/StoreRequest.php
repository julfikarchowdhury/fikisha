<?php

namespace App\Http\Requests\LeaveAssign;

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
    public function rules(): array
    {
        
        return [
            'role_id' => ['required'],
            'type_id' => ['required'],
            'days'    => ['required','numeric'],
        ];
        
    }
 
    public function messages()
    {
        return [
            'role_id.required'=>'The role field is required.',
            'type_id.required'=>'The leave type field is required.'
        ];
    }

}
