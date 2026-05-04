<?php

namespace App\Http\Requests\Merchant;

use App\Models\Backend\Merchant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class UpdateRequest extends FormRequest
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
        $merchant  = Merchant::findOrFail($this->id);
        $userID = $merchant->user_id;
        $user = $merchant->user;

        $nidRule = [$merchant->nid_id ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg', 'max:5098'];
        $nidBackRule = [$merchant->nid_back_id ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg', 'max:5098'];
        $imageRule = [($user && $user->image_id) ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg', 'max:5098'];
        return [
            'province_id'           => ['required'],
            'city_id'               => ['required'],
            'first_name'            => ['required'],
            'last_name'             => ['required'],
            'business_name'         => 'nullable|string|unique:merchants,business_name,' . $this->id,
            'mobile'                => 'required|numeric|digits_between:9,14|unique:users,mobile,' . $userID,
            'email'                 => 'required|string|unique:users,email,' . $userID,
            'status'                => ['required', 'numeric'],
            'location'              => ['nullable'],
            'business_address'      => ['nullable', 'string', 'max:191'],
            'payment_period'        => ['numeric'],
            'discount'              => ['numeric'],
            'nid'                   => $nidRule,
            'nid_back'              => $nidBackRule,
            'image_id'              => $imageRule,
        ];
    }

    public function attributes()
    {
        return [
            'province_id'   => 'province',
            'city_id'       => 'city',
        ];
    }
}
