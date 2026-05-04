<?php

namespace App\Http\Controllers\Backend\HRM;

use App\Http\Controllers\Controller;
use App\Models\Backend\HRM\Weekend;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class WeekendController extends Controller
{
    public function index(){
        $weekends   = Weekend::all();
        return view('backend.hrm.weekend.index',compact('weekends'));
    }
    public function update(Request $request){
        try {
    
            $weekend = Weekend::find($request->id);
            $weekend->is_weekend  = $request->is_weekend;
            $weekend->save();    
            Toastr::success(__('parcel.status_updated_successfully'),__('message.success'));
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        }
    }
}
