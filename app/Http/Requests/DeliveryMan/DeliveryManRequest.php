<?php

namespace App\Http\Requests\DeliveryMan;

use App\Enums\DriverType;
use App\Models\Backend\DeliveryMan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;

class DeliveryManRequest extends FormRequest
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
        $deliveryMan = null;
        if (Request::input('id')) {
            $deliveryMan = DeliveryMan::findOrFail(Request::input('id'));
            $userID = $deliveryMan->user_id;
            $email    = ['required', 'email', 'string', Rule::unique("users", "email")->ignore($userID)];
            $mobile   = ['required', 'numeric', 'digits_between:9,14', Rule::unique("users", "mobile")->ignore($userID)];
            $password = ['nullable'];
        } else {
            $email    = ['required', 'email', 'string', 'unique:users,email'];
            $mobile   = ['required', 'numeric', 'digits_between:9,14', 'unique:users,mobile'];
            $password = ['required', 'min:6'];
        }

        if (Request::input('driver_type') == DriverType::EMPLOYEE) {
            $salary             = ['required', 'numeric'];
            $delivery_charge    = ['nullable', 'numeric'];
            $pickup_charge      = ['nullable', 'numeric'];
            $return_charge      = ['nullable', 'numeric'];
        } else if (Request::input('driver_type') == DriverType::FREELANCER) {
            $salary             = ['nullable', 'numeric'];
            $pickup_charge      = ['required', 'numeric'];
            $delivery_charge    = ['required', 'numeric'];
            $return_charge      = ['required', 'numeric'];
        }

        $imageRequired = empty(optional($deliveryMan)->user?->image_id);
        $frontScanRequired = empty(optional($deliveryMan)->front_side_scan);
        $backScanRequired = empty(optional($deliveryMan)->back_side_scan);
        $licenseRequired = empty(optional($deliveryMan)->driving_license_image_id);
        $regisFrontRequired = empty(optional($deliveryMan)->regis_front_scan);
        $regisBackRequired = empty(optional($deliveryMan)->regis_back_scan);
        $insuranceRequired = empty(optional($deliveryMan)->insurance_crtfy_scan);

        return [
            'name'                      => ['required', 'string', 'max:191'],
            'email'                     => $email,
            'password'                  => $password,
            'mobile'                    => $mobile,
            'address'                   => ['required', 'string', 'max:200'],
            'salary'                    => $salary,
            'pickup_charge'             => $pickup_charge,
            'delivery_charge'           => $delivery_charge,
            'return_charge'             => $return_charge,
            'opening_balance'           => ['nullable', 'numeric'],
            'status'                    => ['required', 'numeric'],
            'image_id'                  => [($imageRequired ? 'required' : 'nullable'), 'image', 'mimes:jpeg,png,jpg', 'max:5098'],
            'driving_license_image_id'  => [($licenseRequired ? 'required' : 'nullable'), 'image', 'mimes:jpeg,png,jpg', 'max:5098'],
            'location'                  => ['required'],
            'driver_type'               => ['required', 'numeric'],
            'front_side_scan'           => [($frontScanRequired ? 'required' : 'nullable'), 'image', 'mimes:jpeg,png,jpg'],
            'back_side_scan'            => [($backScanRequired ? 'required' : 'nullable'), 'image', 'mimes:jpeg,png,jpg'],
            'regis_front_scan'          => [($regisFrontRequired ? 'required' : 'nullable'), 'image', 'mimes:jpeg,png,jpg'],
            'regis_back_scan'           => [($regisBackRequired ? 'required' : 'nullable'), 'image', 'mimes:jpeg,png,jpg'],
            'inspctn_check_scan'        => 'nullable|image|mimes:jpeg,png,jpg',
            'insurance_crtfy_scan'      => [($insuranceRequired ? 'required' : 'nullable'), 'image', 'mimes:jpeg,png,jpg'],
            'tech_c_scan'               => 'nullable|image|mimes:jpeg,png,jpg',

        ];
    }

    public function attributes()
    {
        return [
            'name'                       => trans('validation.attributes.name'),
            'status'                     => trans('validation.attributes.status'),
            'email'                      => trans('validation.attributes.email'),
            'mobile'                     => trans('validation.attributes.phone'),
            'address'                    => trans('validation.attributes.address'),
            'opening_balance'            => trans('validation.attributes.opening_balance'),
            'delivery_charge'            => trans('validation.attributes.delivery_charge'),
            'pickup_charge'              => trans('validation.attributes.pickup_charge'),
            'return_charge'              => trans('validation.attributes.return_charge'),
            'image_id'                   => trans('validation.attributes.image_id'),
            'driving_license_image_id'   => trans('validation.attributes.driving_license_image_id'),
        ];
    }
}
