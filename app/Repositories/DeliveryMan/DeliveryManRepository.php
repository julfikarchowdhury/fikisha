<?php

namespace App\Repositories\DeliveryMan;

use App\Enums\DriverType;
use App\Enums\StatementType;
use App\Enums\Status;
use App\Enums\UserType;
use App\Models\Backend\City;
use App\Models\Backend\Country;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\DeliverymanStatement;
use App\Models\Backend\District;
use App\Models\Backend\Expense;
use App\Models\Backend\Income;
use App\Models\Backend\Town;
use App\Models\Backend\Upload;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Repositories\DeliveryMan\DeliveryManInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DeliveryManRepository implements DeliveryManInterface
{

    public function all()
    {
        return DeliveryMan::with('uploadLicense', 'user')
            ->where(function ($query) {
                $query->whereHas('user', function ($queryUser) {
                    //
                });
            })
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function filter($request)
    {
        return DeliveryMan::with('uploadLicense', 'user')
            ->where(function ($query) use ($request) {
                $query->whereHas('user', function ($queryUser) use ($request) {
                    if ($request->province_id) {
                        $queryUser->where('province_id', $request->province_id);
                    }
                    if ($request->city_id) {
                        $queryUser->where('city_id', $request->city_id);
                    }
                    if ($request->name) {
                        $queryUser->where('name', 'like', '%' . $request->name . '%');
                    }
                    if ($request->email) {
                        $queryUser->where('email', 'like', '%' . $request->email . '%');
                    }
                    if ($request->phone) :
                        $queryUser->where('mobile', 'like', '%' . $request->phone . '%');
                    endif;
                });
            })->orderByDesc('id')->paginate(10);
    }

    public function get($id)
    {
        return DeliveryMan::find($id);
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();
            $deliveryUser                       = new User();
            $deliveryUser->name                 = $request->name;
            $deliveryUser->country_code           = $request->country_code;
            $deliveryUser->mobile               = $request->mobile;
            $deliveryUser->email                = $request->email;
            $deliveryUser->password             = Hash::make($request->password);
            $deliveryUser->address              = $request->address;

            $deliveryUser->nid_number             = $request->nid_number;
            $deliveryUser->location               = $request->location;
            $deliveryUser->latitude               = $request->location_lat;
            $deliveryUser->longitude              = $request->location_long;


            $deliveryUser->status               = $request->status;
            $deliveryUser->permissions          = [];
            $deliveryUser->user_type            = UserType::DELIVERYMAN;
            if ($request->salary !== "") :
                $deliveryUser->salary               = $request->salary;
            endif;
            if (isset($request->image_id) && $request->image_id != null) {
                $deliveryUser->image_id = $this->user_image($request->image_id, $deliveryUser->image_id);
            }
            $deliveryUser->province_id          = $request->province_id;
            $deliveryUser->city_id              = $request->city_id;
            $deliveryUser->save();

            $deliveryMan                                 = new DeliveryMan();
            $deliveryMan->user_id                        = $deliveryUser->id;
            $deliveryMan->vehicle_type                   = $request->vehicle_type;

            $deliveryMan->delivery_lat                   = $request->location_lat;
            $deliveryMan->delivery_long                  = $request->location_long;
            if ($request->driver_type == DriverType::EMPLOYEE) {
                $deliveryMan->delivery_charge            = 0;
                $deliveryMan->pickup_charge              = 0;
                $deliveryMan->return_charge              = 0;
            } else {
                if ($request->delivery_charge !== "") :
                    $deliveryMan->delivery_charge            = $request->delivery_charge;
                endif;
                if ($request->pickup_charge !== "") :
                    $deliveryMan->pickup_charge              = $request->pickup_charge;
                endif;
                if ($request->return_charge !== "") :
                    $deliveryMan->return_charge             = $request->return_charge;
                endif;
            }

            if ($request->opening_balance !== "") :
                $deliveryMan->current_balance           = $request->opening_balance;
                $deliveryMan->opening_balance           = $request->opening_balance;
            endif;
            if (isset($request->driving_license_image_id) && $request->driving_license_image_id != null) {
                $deliveryMan->driving_license_image_id = $this->driving_license_image($request->driving_license_image_id, $deliveryMan->driving_license_image_id);
            }

            //new
            $deliveryMan->province_id          = $request->province_id;
            $deliveryMan->city_id              = $request->city_id;
            $deliveryMan->driver_side_type     = $request->driver_side_type;
            $deliveryMan->driver_type          = $request->driver_type;
            $deliveryMan->hiring_date          = $request->hiring_date;
            $deliveryMan->internal_id_no       = $request->internal_id_no;
            $deliveryMan->residence_address    = $request->residence_address;

            if ($request->front_side_scan) :
                $deliveryMan->front_side_scan  = $this->imageUpload($request->front_side_scan, ''); //
            endif;
            if ($request->back_side_scan) :
                $deliveryMan->back_side_scan   = $this->imageUpload($request->back_side_scan, ''); //
            endif;
            $deliveryMan->years_of_experience  = $request->years_of_experience;
            $deliveryMan->social_security_no   = $request->social_security_no;
            if ($request->year) {
                $deliveryMan->year                 = $request->year;
            }
            $deliveryMan->registration_no      = $request->registration_no;
            $deliveryMan->chassis_no           = $request->chassis_no;
            if ($request->regis_front_scan) :
                $deliveryMan->regis_front_scan = $this->imageUpload($request->regis_front_scan, ''); //
            endif;
            if ($request->regis_back_scan) :
                $deliveryMan->regis_back_scan  = $this->imageUpload($request->regis_back_scan, ''); //
            endif;
            $deliveryMan->colour               = $request->colour;
            if ($request->inspctn_check_scan) :
                $deliveryMan->inspctn_check_scan   = $this->imageUpload($request->inspctn_check_scan, ''); //
            endif;
            $deliveryMan->insurance_no          = $request->insurance_no;
            $deliveryMan->insurance_company     = $request->insurance_company;
            $deliveryMan->insurance_expiry_date = $request->insurance_expiry_date;
            if ($request->insurance_crtfy_scan) :
                $deliveryMan->insurance_crtfy_scan  = $this->imageUpload($request->insurance_crtfy_scan, ''); //
            endif;
            $deliveryMan->tech_control_id       = $request->tech_control_id;
            $deliveryMan->tech_c_expiry_date    = $request->tech_c_expiry_date;
            if ($request->tech_c_scan) :
                $deliveryMan->tech_c_scan           = $this->imageUpload($request->tech_c_scan, ''); //
            endif;
            $deliveryMan->freelance_signed_contract = $request->freelance_signed_contract;
            $deliveryMan->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {

            DB::rollBack();
            return false;
        }
    }

    public function update($id, $request)
    {
        try {
            DB::beginTransaction();
            $deliveryMan                                 = DeliveryMan::findOrFail($id);
            $deliveryMan->vehicle_type                   = $request->vehicle_type;
            if ($request->driver_type == DriverType::EMPLOYEE) {
                $deliveryMan->delivery_charge            = 0;
                $deliveryMan->pickup_charge              = 0;
                $deliveryMan->return_charge              = 0;
            } else {
                if ($request->delivery_charge !== "") :
                    $deliveryMan->delivery_charge            = $request->delivery_charge;
                endif;
                if ($request->pickup_charge !== "") :
                    $deliveryMan->pickup_charge              = $request->pickup_charge;
                endif;
                if ($request->return_charge !== "") :
                    $deliveryMan->return_charge             = $request->return_charge;
                endif;
            }

            if ($request->opening_balance !== "") :
                $deliveryMan->current_balance           = $request->opening_balance;
                $deliveryMan->opening_balance           = $request->opening_balance;
            endif;

            if (isset($request->driving_license_image_id) && $request->driving_license_image_id != null) {
                $deliveryMan->driving_license_image_id = $this->driving_license_image($deliveryMan->driving_license_image_id, $request->driving_license_image_id);
            }

            $deliveryMan->province_id                   = $request->province_id;
            $deliveryMan->city_id                       = $request->city_id;
            $deliveryMan->driver_side_type              = $request->driver_side_type;
            $deliveryMan->delivery_lat                  = $request->location_lat;
            $deliveryMan->delivery_long                 = $request->location_long;
            //new
            $deliveryMan->driver_type          = $request->driver_type;
            if ($request->hiring_date) {
                $deliveryMan->hiring_date          = $request->hiring_date;
            }
            $deliveryMan->internal_id_no       = $request->internal_id_no;
            $deliveryMan->residence_address    = $request->residence_address;

            if ($request->front_side_scan) :
                $deliveryMan->front_side_scan  = $this->imageUpload($request->front_side_scan, $deliveryMan->front_side_scan); //
            endif;
            if ($request->back_side_scan) :
                $deliveryMan->back_side_scan   = $this->imageUpload($request->back_side_scan, $deliveryMan->back_side_scan); //
            endif;
            $deliveryMan->years_of_experience  = $request->years_of_experience;
            $deliveryMan->social_security_no   = $request->social_security_no;
            if ($request->year) {
                $deliveryMan->year            = $request->year;
            }
            $deliveryMan->registration_no      = $request->registration_no;
            $deliveryMan->chassis_no           = $request->chassis_no;
            if ($request->regis_front_scan) :
                $deliveryMan->regis_front_scan = $this->imageUpload($request->regis_front_scan, $deliveryMan->regis_front_scan); //
            endif;
            if ($request->regis_back_scan) :
                $deliveryMan->regis_back_scan  = $this->imageUpload($request->regis_back_scan, $deliveryMan->regis_back_scan); //
            endif;
            $deliveryMan->colour               = $request->colour;
            if ($request->inspctn_check_scan) :
                $deliveryMan->inspctn_check_scan   = $this->imageUpload($request->inspctn_check_scan, $deliveryMan->inspctn_check_scan); //
            endif;
            $deliveryMan->insurance_no          = $request->insurance_no;
            $deliveryMan->insurance_company     = $request->insurance_company;
            $deliveryMan->insurance_expiry_date = $request->insurance_expiry_date;
            if ($request->insurance_crtfy_scan) :
                $deliveryMan->insurance_crtfy_scan  = $this->imageUpload($request->insurance_crtfy_scan, $deliveryMan->insurance_crtfy_scan); //
            endif;
            $deliveryMan->tech_control_id       = $request->tech_control_id;
            $deliveryMan->tech_c_expiry_date    = $request->tech_c_expiry_date;
            if ($request->tech_c_scan) :
                $deliveryMan->tech_c_scan           = $this->imageUpload($request->tech_c_scan, $deliveryMan->tech_c_scan); //
            endif;
            $deliveryMan->freelance_signed_contract = $request->freelance_signed_contract;
            $deliveryMan->save();

            $deliveryUser                       = User::findOrFail($deliveryMan->user_id);
            $deliveryUser->status               = $request->status;
            $deliveryUser->name                 = $request->name;
            $deliveryUser->country_code           = $request->country_code;
            $deliveryUser->mobile               = $request->mobile;
            $deliveryUser->email                = $request->email;
            $deliveryUser->address              = $request->address;


            $deliveryUser->location               = $request->location;
            $deliveryUser->latitude               = $request->location_lat;
            $deliveryUser->longitude              = $request->location_long;

            if ($request->salary !== "") :
                $deliveryUser->salary               = $request->salary;
            endif;
            if ($request->password != null) {
                $deliveryUser->password = Hash::make($request->password);
            }

            if (isset($request->image_id) && $request->image_id != null) {
                $deliveryUser->image_id = $this->user_image($request->image_id, $deliveryUser->image_id);
            }
            $deliveryUser->province_id          = $request->province_id;
            $deliveryUser->city_id              = $request->city_id;
            $deliveryUser->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function delete($id)
    {
        return DeliveryMan::destroy($id);
    }

    public function accountStatus($id)
    {
        try {
            $deliveryMan = DeliveryMan::find($id);
            if (!$deliveryMan) {
                return false;
            }
            $deliveryUser = User::find($deliveryMan->user_id);
            if (!$deliveryUser) {
                return false;
            }
            $deliveryUser->status = $deliveryUser->status == Status::ACTIVE ? Status::INACTIVE : Status::ACTIVE;
            $deliveryUser->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verificationStatus($id)
    {
        try {
            $deliveryMan = DeliveryMan::find($id);
            if (!$deliveryMan) {
                return false;
            }
            $deliveryUser = User::find($deliveryMan->user_id);
            if (!$deliveryUser) {
                return false;
            }
            $deliveryUser->verification_status = $deliveryUser->verification_status == Status::ACTIVE ? Status::INACTIVE : Status::ACTIVE;
            $deliveryUser->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function documentStatus($id)
    {
        try {
            $deliveryMan = DeliveryMan::find($id);
            if (!$deliveryMan) {
                return false;
            }
            $deliveryUser = User::find($deliveryMan->user_id);
            if (!$deliveryUser) {
                return false;
            }
            $deliveryUser->document_status = $deliveryUser->document_status == Status::ACTIVE ? Status::INACTIVE : Status::ACTIVE;
            $deliveryUser->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function user_image($image, $image_id = '')
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath       = public_path('uploads/users');
                $profileImage          = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
                $image_name            = 'uploads/users/' . $profileImage;
            }
            if (blank($image_id)) {
                $upload                = new Upload();
            } else {
                $upload                = Upload::find($image_id);
                unlink($upload->original);
            }
            $upload->original          = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    // for deliveryMan image upload
    public function driving_license_image($image, $image_id = '')
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath       = public_path('uploads/deliveryMan/image');
                $deliveryManImage      = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $deliveryManImage);
                $image_name            = 'uploads/deliveryMan/image/' . $deliveryManImage;
            }
            if (blank($image_id)) {
                $upload                = new Upload();
            } else {
                $upload                = Upload::find($image_id);
                unlink($upload->original);
            }
            $upload->original          = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function imageUpload($image, $image_id = '')
    {

        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath       = public_path('uploads/deliveryMan/image');
                $deliveryManImage      = Str::random(6) . date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $deliveryManImage);
                $image_name            = 'uploads/deliveryMan/image/' . $deliveryManImage;
            }
            if (blank($image_id)) {
                $upload                = new Upload();
            } else {
                $upload                = Upload::find($image_id);
                unlink($upload->original);
            }
            $upload->original          = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deliverymanEarn($type)
    {
        return DeliverymanStatement::where('type', $type)->where('delivery_man_id', Auth::user()->deliveryman->id)->get();
    }

    public function totalCOD($type)
    {
        return DeliverymanStatement::where('type', $type)->where('delivery_man_id', Auth::user()->deliveryman->id)->where('cash_collection', 1)->get();
    }
    public function paymentLogs()
    {
        $data  = [];
        $income    = Income::where('account_head_id', 2)->where('delivery_man_id', Auth::user()->deliveryman->id)->get();
        $expense   = Expense::where('account_head_id', 5)->where('delivery_man_id', Auth::user()->deliveryman->id)->get();
        $data['income']  = $income;
        $data['expense'] = $expense;
        return $data;
    }

    public function parcelPaymentLogs()
    {
        return DeliverymanStatement::orderByDesc('id')->where('delivery_man_id', Auth::user()->deliveryman->id)->where('type', StatementType::EXPENSE)->where('cash_collection', 1)->select(['id', 'type', 'amount', 'date', 'note', 'created_at', 'updated_at'])->get();
    }

    public function all_country()
    {
        return Country::all();
    }

    public function all_city()
    {
        return City::all();
    }

    public function all_district()
    {
        return District::all();
    }

    public function all_town()
    {
        return Town::all();
    }
}
