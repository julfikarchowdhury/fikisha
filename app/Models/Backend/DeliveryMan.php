<?php

namespace App\Models\Backend;

use App\Enums\DriverType;
use App\Enums\RiderStatus;
use App\Enums\StatementType;
use App\Enums\Status;
use App\Models\CashReceivedFromDeliveryman;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DeliveryMan extends Model
{
    use HasFactory, LogsActivity;

    protected $casts = [
        'is_available' => 'integer',
    ];

    protected $table = 'delivery_man';
    protected $fillable = [
        'user_id',
        'status',
        'delivery_charge',
        'pickup_charge',
        'return_charge',
        'opening_balance',
        'province_id',
        'city_id',
    ];


    public function scopeOrderByDesc($query, $data)
    {
        $query->orderBy($data, 'desc');
    }
    /**
    * Activity Log
    */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('DeliveryMan')
        ->logOnly(['user.name', 'current_balance',])
        ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    // Get active row this model.
    public function scopeActive($query)
    {
        $query->where('status', Status::ACTIVE);
    }

    public function getMyStatusAttribute()
    {
        if($this->status == Status::ACTIVE){
            $status = '<span class="badge badge-pill badge-success">'.trans("status." . $this->status).'</span>';
        }else {
            $status = '<span class="badge badge-pill badge-danger">'.trans("status." . $this->status).'</span>';
        }
        return $status;
    }

    public function getDrivingLicenseImageAttribute()
    {
        if (!empty($this->uploadLicense->original['original']) && file_exists(public_path($this->uploadLicense->original['original']))) {
            return static_asset($this->uploadLicense->original['original']);
        }
        return static_asset('images/default/user.png');
    }

    public function uploadLicense()
    {
        return $this->belongsTo(Upload::class, 'driving_license_image_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statements(){
        return $this->hasMany(DeliverymanStatement::class,'delivery_man_id', 'id');
    }
    public function totalIncome(){
        return $this->statements()->where('type',StatementType::INCOME);
    }
    public function totalIncomes(){
        return $this->statements()->whereNot('parcel_id',null)->where('type',StatementType::INCOME);
    }
    public function totalExpense(){
        return $this->statements()->where('type',StatementType::EXPENSE);
    }

    public function totalPaidToCourier(){
        return $this->hasMany(Income::class,'delivery_man_id', 'id')->where('account_head_id',2);
    }
    public function totalCashCollection(){
        return $this->statements()->where(['cash_collection'=>1,'type'=>StatementType::EXPENSE]);
    }
 
    public function frontsideImage()
    {
        return $this->belongsTo(Upload::class, 'front_side_scan', 'id');
    }
    public function backsideImage()
    {
        return $this->belongsTo(Upload::class, 'back_side_scan', 'id');
    }
    public function regisFrontImage()
    {
        return $this->belongsTo(Upload::class, 'regis_front_scan', 'id');
    }
    public function regisBackImage()
    {
        return $this->belongsTo(Upload::class, 'regis_back_scan', 'id');
    }
    public function inspctnCheckImage()
    {
        return $this->belongsTo(Upload::class, 'inspctn_check_scan', 'id');
    }
    public function insuranceCrtfyImage()
    {
        return $this->belongsTo(Upload::class, 'insurance_crtfy_scan', 'id');
    }

    public function techCImage()
    {
        return $this->belongsTo(Upload::class, 'tech_c_scan', 'id');
    }

    public function getMyDriverTypeAttribute(){
        if ($this->driver_type == DriverType::EMPLOYEE) {
            return __('levels.employee');
        }else{
            return __('levels.freelancer');
        }
    }

    public function getRiderStatusLabelAttribute(): string
    {
        $status = (int) ($this->rider_status ?? RiderStatus::APPROVED);
        return RiderStatus::LABELS[$status] ?? 'Unknown';
    }

    public function getAllimageAttribute(){
        $data =[];

        if (!empty($this->frontsideImage->original['original']) && file_exists(public_path($this->frontsideImage->original['original']))) {
            $data['front_side_scan'] = static_asset($this->frontsideImage->original['original']);
        }else{
            $data['front_side_scan'] = static_asset('images/default/blank-image.jpg');
        }

        if (!empty($this->backsideImage->original['original']) && file_exists(public_path($this->backsideImage->original['original']))) {
            $data['back_side_scan'] = static_asset($this->backsideImage->original['original']);
        }else{
            $data['back_side_scan'] = static_asset('images/default/blank-image.jpg');
        }

        if (!empty($this->regisFrontImage->original['original']) && file_exists(public_path($this->regisFrontImage->original['original']))) {
            $data['regis_front_scan'] = static_asset($this->regisFrontImage->original['original']);
        }else{
            $data['regis_front_scan'] = static_asset('images/default/blank-image.jpg');
        }


        if (!empty($this->regisBackImage->original['original']) && file_exists(public_path($this->regisBackImage->original['original']))) {
            $data['regis_back_scan'] = static_asset($this->regisBackImage->original['original']);
        }else{
            $data['regis_back_scan'] = static_asset('images/default/blank-image.jpg');
        }


        if (!empty($this->inspctnCheckImage->original['original']) && file_exists(public_path($this->inspctnCheckImage->original['original']))) {
            $data['inspctn_check_scan'] = static_asset($this->inspctnCheckImage->original['original']);
        }else{
            $data['inspctn_check_scan'] = static_asset('images/default/blank-image.jpg');
        }

        if (!empty($this->insuranceCrtfyImage->original['original']) && file_exists(public_path($this->insuranceCrtfyImage->original['original']))) {
            $data['insurance_crtfy_scan'] = static_asset($this->insuranceCrtfyImage->original['original']);
        }else{
            $data['insurance_crtfy_scan'] = static_asset('images/default/blank-image.jpg');
        }


        if (!empty($this->techCImage->original['original']) && file_exists(public_path($this->techCImage->original['original']))) {
            $data['tech_c_scan'] = static_asset($this->techCImage->original['original']);
        }else{
            $data['tech_c_scan'] = static_asset('images/default/blank-image.jpg');
        }

        return $data;

    }

}
