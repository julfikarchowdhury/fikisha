<?php

namespace App\Http\Requests\Leave\ApplyLeave;

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
            'leave_assign_id'   => ['required'], 
            'leave_from'        => ['required','before_or_equal:leave_to'],
            'leave_to'          => ['required'],
        ];
    }

    public function attributes() 
    {
        return [
            'leave_assign_id' => __('parcel.leave_type'),
            'user_id'         => __('parcel.user')
        ];
    }
}
