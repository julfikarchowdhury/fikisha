<?php

namespace App\Http\Controllers\Backend;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ExtraCostController extends Controller
{
    public function index()
    {
        return view('backend.extra_cost.index');
    }

    public function edit($edit_id)
    {
        return view('backend.extra_cost.index', compact('edit_id'));
    }

    public function update(Request $request, $id)
    {
        try {
            if ($id == 1) {
                $config_in_side         = Config::where('key', 'rush_hour_service_charge')->first();
                $config_in_side->value  = $request->charge;
                $config_in_side->save();

                $config_out_side         = Config::where('key', 'rush_hour_service_outside_charge')->first();
                $config_out_side->value  = $request->outside_charge;
                $config_out_side->save();
            } elseif ($id == 2) {
                $config_in_side         = Config::where('key', 'scheduled_service_charge')->first();
                $config_in_side->value  = $request->charge;
                $config_in_side->save();

                $config_out_side         = Config::where('key', 'scheduled_service_outside_charge')->first();
                $config_out_side->value  = $request->outside_charge;
                $config_out_side->save();
            }
            Toastr::success('Extra Cost updated successfully.', __('message.success'));
            return redirect()->route('extra_cost.index');
        } catch (\Throwable $th) {
            Toastr::error('Something went wrong.', __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function status(Request $request)
    {
        if ($request->charge_id == 1) {
            $config_data            =  Config::where('key', 'rush_hour_service_status')->first();
            if ($config_data->value  == Status::ACTIVE) {
                $config_data->value =  Status::INACTIVE;
            } else {
                $config_data->value =  Status::ACTIVE;
            }
            $config_data->save();
            return $config_data;
        } elseif ($request->charge_id == 2) {
            $config_data            =  Config::where('key', 'scheduled_service_status')->first();
            if ($config_data->value  == Status::ACTIVE) {
                $config_data->value =  Status::INACTIVE;
            } else {
                $config_data->value =  Status::ACTIVE;
            }
            $config_data->save();
            return $config_data;
        }
        return '';
    }
}
