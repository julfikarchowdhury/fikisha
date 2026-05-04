<?php

namespace App\Http\Requests\DutySchedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class UpdateRequest extends FormRequest
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
            'role_id'     => ['required','unique:duty_schedules,role_id,'.Request::input('id')],
            'start_time'  => ['required'],
            'end_time'    => ['required'],
        ];
    }

    public function attributes(){
        return [
            'role_id.required' => __('role.title')
        ];
    }
}
