<?php

namespace App\Models\Backend\HRM;

use App\Enums\Status;
use App\Models\Backend\HRM\LeaveType;
use App\Models\Backend\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveAssign extends Model
{
    use HasFactory;

    public function role(){
        return $this->belongsTo(Role::class,'role_id','id');
    }

    public function leaveType(){
        return $this->belongsTo(LeaveType::class,'type_id','id');
    }

    public function getMyStatusAttribute(){
        if($this->status == Status::ACTIVE){
            return '<span class="badge badge-pill badge-success">'.__('status.'.$this->status).'</span>';
        }elseif($this->status == Status::INACTIVE){
            return '<span class="badge badge-pill badge-danger">'.__('status.'.$this->status).'</span>';
        }
    }
   
}
