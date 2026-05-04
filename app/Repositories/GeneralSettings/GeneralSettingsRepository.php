<?php

namespace App\Repositories\GeneralSettings;

use App\Models\Backend\GeneralSettings;
use App\Models\Backend\Upload;
use App\Repositories\GeneralSettings\GeneralSettingsInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class GeneralSettingsRepository implements GeneralSettingsInterface
{
    protected ?string $lastError = null;

    public function all()
    {
        return GeneralSettings::find(1);
    }

    public function update($request)
    {
        try {
            $this->lastError = null;
            $row                        = GeneralSettings::find(1);
            $row->name                  = $request->name;
            $row->phone                 = $request->phone;
            $row->email                 = $request->email;
            $row->address               = $request->address;
            $row->currency              = $request->currency;
            $row->about                 = $request->about;
            $row->copyright             = $request->copyright;
            $row->par_track_prefix      = Str::upper($request->par_track_prefix);
            $row->invoice_prefix        = Str::upper($request->invoice_prefix);
            $row->order_invoice_prefix  = Str::upper($request->order_invoice_prefix);
            $row->country_code          = $request->country_code;
            $row->default_country       = $request->country;
            if ($request->max_active_parcels_per_rider !== null) :
                $row->max_active_parcels_per_rider = (int) $request->max_active_parcels_per_rider;
            endif;
            if ($request->rider_min_withdrawal_amount !== null) :
                $row->rider_min_withdrawal_amount = (float) $request->rider_min_withdrawal_amount;
            endif;
            if ($request->marketplace_commission_percent !== null) :
                $row->marketplace_commission_percent = (float) $request->marketplace_commission_percent;
            endif;
            if ($request->marketplace_base_fare !== null) :
                $row->marketplace_base_fare = (float) $request->marketplace_base_fare;
            endif;
            if ($request->marketplace_per_km_rate !== null) :
                $row->marketplace_per_km_rate = (float) $request->marketplace_per_km_rate;
            endif;
            if ($request->marketplace_per_kg_rate !== null) :
                $row->marketplace_per_kg_rate = (float) $request->marketplace_per_kg_rate;
            endif;
            if ($request->marketplace_receiver_markup_percent !== null) :
                $row->marketplace_receiver_markup_percent = (float) $request->marketplace_receiver_markup_percent;
            endif;
            if ($request->marketplace_pricing_mode !== null) :
                $row->marketplace_pricing_mode = (string) $request->marketplace_pricing_mode;
            endif;
            if ($request->inside_city_base_fare !== null) :
                $row->inside_city_base_fare = (float) $request->inside_city_base_fare;
            endif;
            if ($request->inside_city_per_km_rate !== null) :
                $row->inside_city_per_km_rate = (float) $request->inside_city_per_km_rate;
            endif;
            if ($request->inside_city_per_kg_rate !== null) :
                $row->inside_city_per_kg_rate = (float) $request->inside_city_per_kg_rate;
            endif;
            if ($request->outside_city_base_fare !== null) :
                $row->outside_city_base_fare = (float) $request->outside_city_base_fare;
            endif;
            if ($request->outside_city_per_km_rate !== null) :
                $row->outside_city_per_km_rate = (float) $request->outside_city_per_km_rate;
            endif;
            if ($request->outside_city_per_kg_rate !== null) :
                $row->outside_city_per_kg_rate = (float) $request->outside_city_per_kg_rate;
            endif;
            if ($request->inside_city_distance !== null) :
                $row->inside_city_distance = (float) $request->inside_city_distance;
            endif;
            if ($request->primary_color) :
                $row->primary_color     = $request->primary_color;
            endif;
            if ($request->text_color) :
                $row->text_color        = $request->text_color;
            endif;

            if (isset($request->logo) && $request->logo != null) {
                $row->logo              = $this->file($request->logo, $row->logo);
            }
            if (isset($request->light_logo) && $request->light_logo != null) {
                $row->light_logo        = $this->file($request->light_logo, $row->light_logo);
            }
            if (isset($request->favicon) && $request->favicon != null) {
                $row->favicon           = $this->file($request->favicon, $row->favicon);
            }
            if (isset($request->mobile_app_logo) && $request->mobile_app_logo != null) {
                if (!Schema::hasColumn('general_settings', 'mobile_app_logo')) {
                    throw new RuntimeException(
                        "Database column 'general_settings.mobile_app_logo' is missing. Run this SQL once: " .
                        "ALTER TABLE general_settings ADD COLUMN mobile_app_logo INT NULL AFTER favicon;"
                    );
                }
                $row->mobile_app_logo   = $this->file($request->mobile_app_logo, $row->mobile_app_logo);
            }
            $row->save();
            return true;
        } catch (\Throwable $th) {
            $this->lastError = $th->getMessage();
            Log::error('General settings update failed.', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
            return false;
        }
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function file($image, $image_id = '')
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath       = public_path('uploads/settings');
                $profileImage          = date('YmdHis') . random_int(1000, 9999) . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
                $image_name            = 'uploads/settings/' . $profileImage;
            }
            if (blank($image_id)) {
                $upload           = new Upload();
            } else {
                $upload           = Upload::find($image_id);
                if (file_exists($upload->original)) {
                    unlink($upload->original);
                }
            }
            $upload->original     = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            Log::error('General settings file upload failed.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}
