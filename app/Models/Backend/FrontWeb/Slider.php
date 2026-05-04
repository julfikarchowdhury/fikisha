<?php

namespace App\Models\Backend\FrontWeb;

use App\Enums\Status;
use App\Models\Backend\Upload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    public function upload(){
            return $this->belongsTo(Upload::class,'slider','id');
    }
  
    public function getSliderImageAttribute(){

        if($this->slider && $this->upload && File::exists(public_path($this->upload->original['original']))):
            return  static_asset($this->upload->original['original']);
        else:
            return static_asset('images/default/blank-image.jpg');
        endif;
    }

    public function getMyStatusAttribute()
    {
        if ($this->status == Status::ACTIVE) {
            return '<span class="badge rounded-pill bg-success">' . __('levels.active') . '</span>';
        } elseif ($this->status == Status::INACTIVE) {
            return '<span class="badge rounded-pill bg-danger">' . __('levels.inactive') . '</span>';
        }
    }
}
