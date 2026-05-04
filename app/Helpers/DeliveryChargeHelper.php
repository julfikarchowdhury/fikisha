<?php

namespace App\Helpers;

use App\Enums\DeliveryType;
use App\Enums\WhoPays;
use App\Models\Backend\ShippingChargeOption;
use App\Models\Backend\ShippingType;

class DeliveryChargeHelper
{
    /**
     * Delivery Charge
     * 
     * @param  mixed $request
     */
    public function deliveryCharge($request)
    {
        $distanceKm = (float) ($request->total_distance_km ?? $request->distance_km ?? 0);
        $weightKg = (float) ($request->weight ?? $request->total_weight ?? 0);
        $whoPays = (int) ($request->who_pays_either ?? 0);
        $breakdown = $this->marketplacePricingBreakdown($distanceKm, $weightKg, $whoPays);
        return $breakdown['final'];
    }

    public function marketplacePricingBreakdown(float $distanceKm, float $weightKg, int $whoPays): array
    {
        $pricingMode = (string) (settings()->marketplace_pricing_mode ?? 'city');
        if ($pricingMode !== 'city') {
            $pricingMode = 'city';
        }
        $zone = 'inside_city';
        if ($pricingMode === 'city') {
            $insideThreshold = (float) (settings()->inside_city_distance ?? 0);
            $isInside = $insideThreshold <= 0 ? true : $distanceKm <= $insideThreshold;

            if ($isInside) {
                $zone = 'inside_city';
                $baseFare = (float) (settings()->inside_city_base_fare ?? 0);
                $perKmRate = (float) (settings()->inside_city_per_km_rate ?? 0);
                $perKgRate = (float) (settings()->inside_city_per_kg_rate ?? 0);
            } else {
                $zone = 'outside_city';
                $baseFare = (float) (settings()->outside_city_base_fare ?? 0);
                $perKmRate = (float) (settings()->outside_city_per_km_rate ?? 0);
                $perKgRate = (float) (settings()->outside_city_per_kg_rate ?? 0);
            }
        } else {
            $zone = 'marketplace';
            $baseFare = (float) (settings()->marketplace_base_fare ?? 0);
            $perKmRate = (float) (settings()->marketplace_per_km_rate ?? 0);
            $perKgRate = (float) (settings()->marketplace_per_kg_rate ?? 0);
        }

        if ($baseFare == 0.0 && $perKmRate == 0.0 && $perKgRate == 0.0) {
            $insideThreshold = (float) (settings()->inside_city_distance ?? 0);
            $isInside = $insideThreshold <= 0 ? true : $distanceKm <= $insideThreshold;
            if ($isInside) {
                $zone = 'inside_city';
                $baseFare = (float) (settings()->inside_city_base_fare ?? 0);
                $perKmRate = (float) (settings()->inside_city_per_km_rate ?? 0);
                $perKgRate = (float) (settings()->inside_city_per_kg_rate ?? 0);
            } else {
                $zone = 'outside_city';
                $baseFare = (float) (settings()->outside_city_base_fare ?? 0);
                $perKmRate = (float) (settings()->outside_city_per_km_rate ?? 0);
                $perKgRate = (float) (settings()->outside_city_per_kg_rate ?? 0);
            }
        }

        $distanceCharge = round($distanceKm * $perKmRate, 2);
        $weightCharge = round($weightKg * $perKgRate, 2);
        $base = $baseFare + $distanceCharge + $weightCharge;
        $base = round($base, 2);

        $receiverMarkupPercent = (float) (settings()->marketplace_receiver_markup_percent ?? 0);
        $markup = 0.0;
        if ($whoPays === WhoPays::RECIPIENT && $receiverMarkupPercent > 0) {
            $markup = round(($base * $receiverMarkupPercent) / 100, 2);
        }

        return [
            'base' => $base,
            'markup' => $markup,
            'final' => round($base + $markup, 2),
            'zone' => $zone,
            'distance_km' => round($distanceKm, 2),
            'weight_kg' => round($weightKg, 2),
            'base_fare' => round($baseFare, 2),
            'per_km_rate' => round($perKmRate, 2),
            'per_kg_rate' => round($perKgRate, 2),
            'distance_charge' => $distanceCharge,
            'weight_charge' => $weightCharge,
            'receiver_markup_percent' => round($receiverMarkupPercent, 2),
        ];
    }

    /**
     * Calculate total cost
     * 
     * @param  mixed $data
     */
    public function calculate_total_cost($data)
    {
        $volume_cost = $this->calculate_costs($data)['volume_cost'];
        $weight_cost = $this->calculate_costs($data)['weight_cost'];
        $total_cost  = ($data['basic_service_fee'] + $volume_cost + $weight_cost);
        return $total_cost;
    }


    /**
     * Calculate volume-based and weight-based costs
     * 
     * @param  mixed $data
     */
    public function calculate_costs($data)
    {
        $volume_frames = $this->calculate_frames($data['parcel_volume_cubic_meters'], $data['basic_volume_frame_value'], $data['with_first_frame']);
        $weight_frames = $this->calculate_frames($data['parcel_weight_kg'], $data['basic_weight_frame_value'], $data['with_first_frame']);
        $volume_cost   = $volume_frames * $data['volume_frame_cost'];
        $weight_cost   = $weight_frames * $data['weight_frame_cost'];
        return  [
            'volume_cost' => $volume_cost,
            'weight_cost' => $weight_cost
        ];
    }

    # 

    /**
     * Calculate the number of frames needed
     * 
     * @param  mixed $value
     * @param  mixed $basic_frame_value
     * @param  mixed $with_first_frame
     */
    public function  calculate_frames($value, $basic_frame_value, $with_first_frame = null)
    {
        if ($with_first_frame) {
            if (!empty($value) && $value > 0) :
                return ceil($value / $basic_frame_value);
            endif;
            return 0;
        } else {
            if (!empty($value) && $value > 0) :
                return  ceil($value / $basic_frame_value) - 1;
            endif;
            return 0;
        }
    }

    /**
     * Outside City Total Cost
     * 
     * @param  mixed $request
     * @param  mixed $shipping_type
     */
    public function outsideCityTotalCost($request, $shipping_type)
    {
        try {
            $total_cost = 0;
            if ($shipping_type->id == 5) :
                //door to door
                $total_cost = $this->outside_Door2Door($request);
            elseif ($shipping_type->id == 6) :
                //door to hub
                $total_cost  = $this->outside_Door2Hub($request);
            elseif ($shipping_type->id == 7) :
                //hub to door
                $total_cost = $this->outside_Hub2Door($request);
            elseif ($shipping_type->id == 8) :
                //hub to hub
                $total_cost = $this->outside_Hub2Hub($request);
            endif;
            return $total_cost;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * outside city calculation
     * 
     * @param  mixed $request
     * @param  mixed $shippingType
     * @param  mixed $basic_price
     */
    public function serviceCost($request, $shippingType, $basic_price)
    {
        $total_cost = 0;
        $total_weight_kg    = $request->weight ?? 0;
        $total_cubic_meters = $request->total_cbm ?? 0;

        $data['parcel_weight_kg']           = $total_weight_kg;
        $data['parcel_volume_cubic_meters'] = $total_cubic_meters; //total cubic meters

        # Rules
        $data['basic_service_fee']        = $basic_price;
        $data['volume_frame_cost']        = $shippingType->addi_volume_price;
        $data['weight_frame_cost']        = $shippingType->addi_weight_price;
        $data['basic_volume_frame_value'] = $shippingType->basic_volume_frame_value;
        $data['basic_weight_frame_value'] = $shippingType->basic_weight_frame_value;
        $data['with_first_frame'] = null;

        # Display the result
        $total_cost =   $this->calculate_total_cost($data);
        return $total_cost;
    }

    /**
     * Outside Door to Door
     * 
     * @param  mixed $request
     */
    public function outside_Door2Door($request)
    {
        /*
        1.Outside city Door to Door
        2.Hub province 1 to Hub Province 2 (Outside Province Service)
        */
        $outside_door_to_door         = ShippingType::find(5);
        $total_cost                 = $this->outSideCityCost($request, $outside_door_to_door);
        // Calculation result
        return $total_cost;
    }

    /**
     * Outside Door to Hub
     * 
     * @param  mixed $request
     */
    public function outside_Door2Hub($request)
    {
        /*
        1.Outside city door to hub
        2.Hub province 1 to Hub Province 2 (Outside Province Service)
        */
        $outside_door_to_hub         = ShippingType::find(6);
        $total_cost           = $this->outSideCityCost($request, $outside_door_to_hub);

        // Calculation result
        return $total_cost;
    }

    /**
     * Outside Hub to Door
     * 
     * @param  mixed $request
     */
    public function outside_Hub2Door($request)
    {
        /*
        1.Outside city hub to door
        2.Hub Province 2 to Door province 2 (Inside Province Service)
        */
        $outside_hub_to_door         = ShippingType::find(7);
        $total_cost           = $this->outSideCityCost($request, $outside_hub_to_door);

        // Calculation result
        return $total_cost;
    }

    /**
     * Outside Hub to Hub
     * 
     * @param  mixed $request
     */
    public function outside_Hub2Hub($request)
    {
        /*
        1.Outside city hub to hub
        2.Hub province 1 to Hub Province 2 (Outside Province Service)
        */
        $outside_hub_to_hub          = ShippingType::find(8);
        $total_cost   = $this->outSideCityCost($request, $outside_hub_to_hub);

        // Calculation result
        return $total_cost;
    }

    /**
     * OutSide City Cost
     *
     * @param  mixed $request
     * @param  mixed $shipping_type
     */
    public function outSideCityCost($request, $shipping_type)
    {
        $total_cost = 0;
        $option = ShippingChargeOption::where('shipping_type_id', $shipping_type->id)
            ->where('from_km', '<', $request->total_distance_km)
            ->where('to_km', '>=', $request->total_distance_km)->first();
        if ($option) :
            $total_cost = $this->serviceCost($request, $shipping_type, $option->basic_price);
        endif;
        return $total_cost;
    }

    /**
     * init instance
     */
    public static function instance()
    {
        return new DeliveryChargeHelper();
    }
}
