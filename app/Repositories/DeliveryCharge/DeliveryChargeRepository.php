<?php

namespace App\Repositories\DeliveryCharge;

use App\Models\Backend\DeliveryCharge;
use App\Models\Backend\Deliverycategory;
use App\Repositories\DeliveryCharge\DeliveryChargeInterface;
use App\Enums\Status;
use App\Enums\UserType;
use Illuminate\Support\Arr;

class DeliveryChargeRepository implements DeliveryChargeInterface
{
    public function allGet()
    {
        $dCharges      = DeliveryCharge::with('category')->orderBy('position')->get();
        $categoryWise  = $dCharges->groupBy('category_id');
        return Arr::collapse($categoryWise);
        $Charges = [];
        foreach ($categoryWise as $key => $Deliverycharge) {
            foreach ($Deliverycharge as $charge) {
                $Charges[] = $charge;
            }
        }
        return $Charges;
    }
    public function getAllCharge()
    {
        $dCharges      = DeliveryCharge::with('category')->orderBy('position')->get();
        $categoryWise  = $dCharges->groupBy('category_id');
        return Arr::collapse($categoryWise);
    }
    public function all()
    {
        return DeliveryCharge::with('category')->orderBy('position')->paginate(10);
    }

    public function filter($request)
    {
        return DeliveryCharge::with('category')->where(function ($query) use ($request) {
            if ($request->category) {
                $query->where('category_id', $request->category);
            }
            if ($request->weight) :
                $query->where('weight', $request->weight);
            endif;
        })->orderBy('position')->paginate(10);
    }

    public function categories()
    {
        return Deliverycategory::all();
    }

    public function get($id)
    {
        return DeliveryCharge::find($id);
    }

    public function store($request)
    {


        try {

            $delivery_charge                     = new DeliveryCharge();

            $delivery_charge->shipping_type    = $request->shipping_type;
            $delivery_charge->delivery_type_id = $request->delivery_type_id;
            //from
            $delivery_charge->from_country_id  = $request->from_country_id;
            $delivery_charge->from_city_id     = $request->from_city_id;
            $delivery_charge->from_district_id = $request->from_district_id;
            $delivery_charge->from_town_id     = $request->from_town_id;
            $delivery_charge->from_portal_code = $request->from_portal_code;
            //end from

            //to
            $delivery_charge->to_country_id    = $request->to_country_id;
            $delivery_charge->to_city_id       = $request->to_city_id;
            $delivery_charge->to_district_id   = $request->to_district_id;
            $delivery_charge->to_town_id       = $request->to_town_id;
            $delivery_charge->to_portal_code  = $request->to_portal_code;
            //to


            //charge
            $delivery_charge->dtd_start_weight       = $request->dtd_start_weight ?? 0;
            $delivery_charge->dtd_end_weight         = $request->dtd_end_weight ?? 0;
            $delivery_charge->dtd_s_e_rate           = $request->dtd_s_e_rate ?? 0;
            $delivery_charge->dtd_additional_weight  = $request->dtd_additional_weight ?? 0;
            $delivery_charge->dtd_additional_rate    = $request->dtd_additional_rate ?? 0;

            $delivery_charge->dth_start_weight       = $request->dth_start_weight ?? 0;
            $delivery_charge->dth_end_weight         = $request->dth_end_weight ?? 0;
            $delivery_charge->dth_s_e_rate           = $request->dth_s_e_rate ?? 0;
            $delivery_charge->dth_additional_weight  = $request->dth_additional_weight ?? 0;
            $delivery_charge->dth_additional_rate    = $request->dth_additional_rate ?? 0;

            $delivery_charge->hth_start_weight       = $request->hth_start_weight ?? 0;
            $delivery_charge->hth_end_weight         = $request->hth_end_weight ?? 0;
            $delivery_charge->hth_s_e_rate           = $request->hth_s_e_rate ?? 0;
            $delivery_charge->hth_additional_weight  = $request->hth_additional_weight ?? 0;
            $delivery_charge->hth_additional_rate    = $request->hth_additional_rate ?? 0;

            $delivery_charge->htd_start_weight       = $request->htd_start_weight;
            $delivery_charge->htd_end_weight         = $request->htd_end_weight;
            $delivery_charge->htd_s_e_rate           = $request->htd_s_e_rate;
            $delivery_charge->htd_additional_weight  = $request->htd_additional_weight;
            $delivery_charge->htd_additional_rate    = $request->htd_additional_rate;
            //end charge


            $delivery_charge->position     = $request->position;
            $delivery_charge->status       = $request->status;
            $delivery_charge->save();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update($request)
    {
        try {

            $delivery_charge               = DeliveryCharge::find($request->id);

            $delivery_charge->shipping_type    = $request->shipping_type;
            $delivery_charge->delivery_type_id = $request->delivery_type_id;
            //from
            $delivery_charge->from_country_id  = $request->from_country_id;
            $delivery_charge->from_city_id     = $request->from_city_id;
            $delivery_charge->from_district_id = $request->from_district_id;
            $delivery_charge->from_town_id     = $request->from_town_id;
            $delivery_charge->from_portal_code = $request->from_portal_code;
            //end from

            //to
            $delivery_charge->to_country_id    = $request->to_country_id;
            $delivery_charge->to_city_id       = $request->to_city_id;
            $delivery_charge->to_district_id   = $request->to_district_id;
            $delivery_charge->to_town_id       = $request->to_town_id;
            $delivery_charge->to_portal_code  = $request->to_portal_code;
            //to

            //charge
            $delivery_charge->dtd_start_weight       = $request->dtd_start_weight ?? 0;
            $delivery_charge->dtd_end_weight         = $request->dtd_end_weight ?? 0;
            $delivery_charge->dtd_s_e_rate           = $request->dtd_s_e_rate ?? 0;
            $delivery_charge->dtd_additional_weight  = $request->dtd_additional_weight ?? 0;
            $delivery_charge->dtd_additional_rate    = $request->dtd_additional_rate ?? 0;

            $delivery_charge->dth_start_weight       = $request->dth_start_weight ?? 0;
            $delivery_charge->dth_end_weight         = $request->dth_end_weight ?? 0;
            $delivery_charge->dth_s_e_rate           = $request->dth_s_e_rate ?? 0;
            $delivery_charge->dth_additional_weight  = $request->dth_additional_weight ?? 0;
            $delivery_charge->dth_additional_rate    = $request->dth_additional_rate ?? 0;

            $delivery_charge->hth_start_weight       = $request->hth_start_weight ?? 0;
            $delivery_charge->hth_end_weight         = $request->hth_end_weight ?? 0;
            $delivery_charge->hth_s_e_rate           = $request->hth_s_e_rate ?? 0;
            $delivery_charge->hth_additional_weight  = $request->hth_additional_weight ?? 0;
            $delivery_charge->hth_additional_rate    = $request->hth_additional_rate ?? 0;

            $delivery_charge->htd_start_weight       = $request->htd_start_weight;
            $delivery_charge->htd_end_weight         = $request->htd_end_weight;
            $delivery_charge->htd_s_e_rate           = $request->htd_s_e_rate;
            $delivery_charge->htd_additional_weight  = $request->htd_additional_weight;
            $delivery_charge->htd_additional_rate    = $request->htd_additional_rate;
            //end charge

            $delivery_charge->position           = $request->position;
            $delivery_charge->status             = $request->status;
            $delivery_charge->save();


            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    public function delete($id)
    {
        return DeliveryCharge::destroy($id);
    }
}
