<?php

namespace App\Models\Backend\HRM;

use App\Enums\LeaveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Backend\HRM\LeaveType;
use App\Models\Backend\Role;
use App\Models\Backend\Upload;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class LeaveRequest extends Model
{
    use HasFactory;

   public function user(){
        return $this->belongsTo(User::class,'user_id','id');
   }

    public function leaveType(){
        return $this->belongsTo(LeaveType::class,'type_id','id');
    }
    public function role(){
        return $this->belongsTo(Role::class,'role_id','id');
    }
    public function leaveAssign(){
        return $this->belongsTo(LeaveAssign::class,'leave_assign_id','id');
    }
   

    public function upload(){
        return $this->belongsTo(Upload::class,'file','id');
    }

    public function getFilePathAttribute()
    { 
        if ($this->upload && !empty($this->upload->original['original']) && File::exists(public_path($this->upload->original['original']))) {
            return static_asset($this->upload->original['original']);
        }
        return static_asset('/') ;
    }

 
    public function getMyStatusAttribute(){
        if($this->status == LeaveStatus::PENDING){
            return '<span class="badge badge-pill badge-warning">'.__('parcel.pending').'</span>';
        }elseif($this->status == LeaveStatus::APPROVED){
            return '<span class="badge badge-pill badge-success">'.__('parcel.approved').'</span>';
        }elseif($this->status == LeaveStatus::REJECTED){
            return '<span class="badge badge-pill badge-danger">'.__('parcel.rejected').'</span>';
        }
    }
    public function getMyStatusColorAttribute(){
        if($this->status == LeaveStatus::PENDING){
            return '<span class=" text-warning">'.__('parcel.pending').'</span>';
        }elseif($this->status == LeaveStatus::APPROVED){
            return '<span class=" text-success">'.__('parcel.approved').'</span>';
        }elseif($this->status == LeaveStatus::REJECTED){
            return '<span class=" text-danger">'.__('parcel.rejected').'</span>';
        }
    }

    public function getTotalDaysAttribute(){
        $from  = Carbon::parse($this->leave_from)->startOfDay()->toDateTimeString();
        $to    = Carbon::parse($this->leave_to)->endOfDay()->addMinute(1)->toDateTimeString();
        return Carbon::parse($from)->diffInDays($to);
    }
 
}
