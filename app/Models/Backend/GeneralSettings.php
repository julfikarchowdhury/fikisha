<?php

namespace App\Models\Backend;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class GeneralSettings extends Model
{
    use HasFactory,LogsActivity;


    protected $fillable = [
        'phone',
        'name',
        'tracking_id',
        'details',
        'prefix',
        'mobile_app_logo',
        'location_system',
        'max_active_parcels_per_rider',
        'rider_min_withdrawal_amount',
        'marketplace_commission_percent',
        'marketplace_base_fare',
        'marketplace_per_km_rate',
        'marketplace_per_kg_rate',
        'marketplace_receiver_markup_percent',
        'marketplace_pricing_mode',
        'inside_city_base_fare',
        'inside_city_per_km_rate',
        'inside_city_per_kg_rate',
        'outside_city_base_fare',
        'outside_city_per_km_rate',
        'outside_city_per_kg_rate',
        'inside_city_distance',
    ];

    public function getActivitylogOptions(): LogOptions
    {

        $logAttributes = [

            'phone',
            'name',
            'tracking_id',
            'details',
            'prefix',
            'mobile_app_logo',
            'max_active_parcels_per_rider',
            'rider_min_withdrawal_amount',
            'marketplace_commission_percent',
            'marketplace_base_fare',
            'marketplace_per_km_rate',
            'marketplace_per_kg_rate',
            'marketplace_receiver_markup_percent',
            'marketplace_pricing_mode',
            'inside_city_base_fare',
            'inside_city_per_km_rate',
            'inside_city_per_kg_rate',
            'outside_city_base_fare',
            'outside_city_per_km_rate',
            'outside_city_per_kg_rate',
            'inside_city_distance',
        ];
        return LogOptions::defaults()
        ->useLogName('General Settings')
        ->logOnly($logAttributes)
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    // Get single row in Upload table.
    public function rxlogo()
    {
        return $this->belongsTo(Upload::class, 'logo', 'id');
    }
    public function lightlogo()
    {
        return $this->belongsTo(Upload::class, 'light_logo', 'id');
    }
    public function rxfavicon()
    {
        return $this->belongsTo(Upload::class, 'favicon', 'id');
    }
    public function rxMobileAppLogo()
    {
        return $this->belongsTo(Upload::class, 'mobile_app_logo', 'id');
    }

    public function getLogoImageAttribute()
    {
        if (!empty($this->rxlogo->original['original']) && file_exists(public_path($this->rxlogo->original['original']))) {
            return static_asset($this->rxlogo->original['original']);
        }
        return static_asset('images/default/logo.png');
    }

    public function getLogoImageTAttribute()
    {
        if (!empty($this->rxlogo->original['original']) && file_exists(public_path($this->rxlogo->original['original']))) {
            return static_asset($this->rxlogo->original['original']);
        }
        return static_asset('images/default/logo.png');
    }

    public function getLightLogoImageAttribute()
    {
        if (!empty($this->lightlogo->original['original']) && file_exists(public_path($this->lightlogo->original['original']))) {
            return static_asset($this->lightlogo->original['original']);
        }
        return static_asset('images/default/light-logo.png');
    }

    public function getFaviconImageAttribute()
    {
        if (!empty($this->rxfavicon->original['original']) && file_exists(public_path($this->rxfavicon->original['original']))) {
            return static_asset($this->rxfavicon->original['original']);
        }
        return static_asset('images/default/favicon.png');
    }

    public function getMobileAppLogoImageAttribute()
    {
        if (!empty($this->rxMobileAppLogo->original['original']) && file_exists(public_path($this->rxMobileAppLogo->original['original']))) {
            return static_asset($this->rxMobileAppLogo->original['original']);
        }
        return static_asset('images/default/logo.png');
    }

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function excenseRate(){
        return $this->belongsTo(Currency::class,'currency','symbol');
    }

    public function defaultCountry(){
        return $this->belongsTo(Country::class,'default_country','id');
    }
}
