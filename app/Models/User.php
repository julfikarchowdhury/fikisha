<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Backend\Upload;
use App\Models\Backend\Department;
use App\Models\Backend\Designation;
use App\Enums\Status;
use App\Enums\UserType;
use App\Enums\VerificationStatus;
use App\Models\Backend\Account;
use App\Models\Backend\City;
use App\Models\Backend\Country;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\District;
use App\Models\Backend\Merchant;
use App\Models\Backend\Province;
use App\Models\Backend\Role;
use App\Models\Backend\Salary;
use App\Models\Backend\Town;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'address',
        'password',
        'image_id',
        'facebook_id',
        'google_id',
        'user_type',
        'document_status',
        'email_verified_at'
    ];


    /**
     * Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('User')
            ->logOnly(['name', 'email'])
            ->setDescriptionForEvent(fn (string $eventName) => "{$eventName}");
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_reset_otp_expires_at' => 'datetime',
        'password_reset_verified_at' => 'datetime',
        'permissions'       => 'array'
    ];

    // Get single row in Upload table.
    public function upload()
    {
        return $this->belongsTo(Upload::class, 'image_id', 'id');
    }

    // Get single row in Department table.
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    // Get single row in Designation table.
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }

    // Get all row. Descending order using scope.
    public function scopeOrderByDesc($query, $data)
    {
        $query->orderBy($data, 'desc');
    }

    public function getImageAttribute()
    {
        if (!empty($this->upload->original['original']) && file_exists(public_path($this->upload->original['original']))) {
            return static_asset($this->upload->original['original']);
        }
        return static_asset('images/default/user.png');
    }

    public function getMyStatusAttribute()
    {
        if ($this->status == Status::ACTIVE) {
            $status = '<span class="badge badge-pill badge-success">' . trans("status." . $this->status) . '</span>';
        } else {
            $status = '<span class="badge badge-pill badge-danger">' . trans("status." . $this->status) . '</span>';
        }
        return $status;
    }

    public function getMyVerificationStatusAttribute()
    {
        if ($this->verification_status == Status::ACTIVE) {
            $verification_status = '<span class="badge badge-pill badge-success">' . trans("verification." . VerificationStatus::VERIFIED) . '</span>';
        } else {
            $verification_status = '<span class="badge badge-pill badge-danger">' . trans("verification." . VerificationStatus::NOT_VERIFIED) . '</span>';
        }
        return $verification_status;
    }

    public function getMyDocumentStatusAttribute()
    {
        if ($this->document_status == Status::ACTIVE) {
            $document_status = '<span class="badge badge-pill badge-success">' . trans("document." . Status::ACTIVE) . '</span>';
        } else {
            $document_status = '<span class="badge badge-pill badge-danger">' . trans("document." . Status::INACTIVE) . '</span>';
        }
        return $document_status;
    }

    public function getCountryCodeAttribute()
    {
        return '+' . @$this->country_code;
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'id', 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function deliveryman()
    {
        return $this->belongsTo(DeliveryMan::class, 'id', 'user_id');
    }

    public function salary()
    {
        return $this->hasMany(Salary::class, 'user_id', 'id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'user_id', 'id');
    }

    // Get single row in Country table.
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    // Get single row in province table.
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    // Get single row in City table.
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    // Get single row in District table.
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    // Get single row in Town table.
    public function town()
    {
        return $this->belongsTo(Town::class, 'town_id', 'id');
    }

    public function getMyUserTypeAttribute()
    {
        $user_type_data = '';
        if ($this->user_type == UserType::ADMIN) {
            $user_type_data = __('Admin');
        } elseif ($this->user_type == UserType::MERCHANT) {
            $user_type_data = __('Customer');
        } elseif ($this->user_type == UserType::DELIVERYMAN) {
            $user_type_data = __('Deliveryman');
        } elseif ($this->user_type == UserType::INCHARGE) {
            $user_type_data = __('In charge');
        }
        return $user_type_data;
    }
}
