<?php

namespace App\Http\Requests\FrontWeb\Slider;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         
        $slider = ['mimes:png,jpg,gif'];
        
        return [
            'title'               => ['required','max:120'],
            'slider'              => $slider,
            'small_title'         => ['required'],
            'position'            => ['numeric']
        ];
    }
}
