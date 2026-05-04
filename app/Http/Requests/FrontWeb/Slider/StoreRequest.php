<?php

namespace App\Http\Requests\FrontWeb\Slider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

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
        if(Request::input('update')):
            $slider = ['mimes:png,jpg,gif'];
        else:
            $slider = ['required','mimes:png,jpg,gif'];
        endif;

        return [
            'title'               => ['required','max:120'],
            'slider'              => $slider,
            'small_title'         => ['required'],
            'position'            => ['numeric']
        ];
    }
}
