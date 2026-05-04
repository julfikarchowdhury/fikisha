<?php

namespace App\Models\Backend;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'registration_number',
        'capacity',
        'size',
        'status',
        'image_id',
    ];

    public function getMyStatusAttribute()
    {
        if ($this->status == Status::ACTIVE) {
            return '<span class="badge rounded-pill bg-success">' . __('status.' . $this->status) . '</span>';
        } elseif ($this->status == Status::INACTIVE) {
            return '<span class="badge rounded-pill bg-danger">' . __('status.' . $this->status) . '</span>';
        }
    }

}
