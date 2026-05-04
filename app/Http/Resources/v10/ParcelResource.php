<?php

namespace App\Http\Resources\v10;

use Illuminate\Http\Resources\Json\JsonResource;

class ParcelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            "id"                    => $this->id,
            "currency"              => settings()->currency,
            "tracking_id"           => $this->tracking_id,
            "merchant_id"           => $this->merchant_id,
            "merchant_name"         => $this->merchant->business_name,
            "merchant_user_name"    => $this->merchant->user->name,
            "merchant_user_email"   => $this->merchant->user->email,
            "merchant_mobile"       => $this->merchant->user->mobile,
            "merchant_address"      => $this->merchant->address,
            "sender_name"           => $this->sender_first_name . ' ' . $this->sender_last_name,
            "sender_company_name"   => $this->sender_company_name,
            "sender_email"          => $this->sender_email,
            "sender_phone"          => $this->sender_phone,
            "pickup_address"          => $this->pickup_address,
            "fromProvinceName"          => $this->fromProvince->name,
            "fromProvinceCode"          => $this->fromProvince->province_code,
            "customer_name"         => $this->customer_first_name . ' ' . $this->customer_last_name,
            "customer_company_name"         => $this->customer_company_name??'',
            "receiver_email"        => (string)$this->receiver_email,
            "customer_phone"        => (string)$this->customer_phone,
            "customer_address"      => $this->customer_address,
            "customer_province_name"      => $this->toProvince->name??'',
            "customer_province_code"      => $this->toProvince->province_code??'',
            "invoice_no"            => (string)$this->invoice_no,
            "weight"                => (@$this->items->sum('total_weight') + @$this->cbm_details['total_weight']). ' '.optional($this->deliveryCategory)->title,
            "total_shipping_fee" => (string)($this->total_delivery_amount + $this->vat_amount - $this->discount_amount),
            "discount_amount" => (string)$this->discount_amount,
            "total_parcel_value" => (string)$this->total_parcel_value,
            "cod_amount"            => (string)$this->cod_amount,
            "vat_amount"            => (string)$this->vat_amount,
            "current_payable"       => (string)$this->current_payable,
            "cash_collection"       => (string)$this->cash_collection,
            "delivery_type_id"      => (int) $this->delivery_type_id,
            "deliveryType"          => trans("deliveryType.".$this->delivery_type_id),
            "status"                => (int) $this->status,
            "statusName"            => trans("parcelStatus.".$this->status),
            'pickup_date'           => dateFormat($this->pickup_date),
            'delivery_date'         => dateFormat($this->delivery_date),
            'created_at'            => $this->created_at->format('d M Y, h:i A'),
            'updated_at'            => $this->updated_at->format('d M Y, h:i A'),
            'parcel_date'           => dateFormat($this->created_at) ,
            'parcel_time'           => date('h:i a', strtotime($this->created_at)) ,
            'pickup_location'       => $this->pickup_location ,
            'drop_location'         => $this->drop_location ,
            'shipping_title'        => $this->ShippingType->title??'',
            'delivery_title'        => $this->DelivType->title??'',
            'from_state_id'        => (string)$this->from_state_id,
            'to_state_id'        => (string)$this->to_state_id,

            'shipping_type'      => (string)$this->shipping_type,
            'from_point_type'        => (string)$this->from_point_type,
            'fromCityName'        => $this->fromCity->name??'',
            'fromHubCityName'        => $this->fromHub->city->name??'',
            'fromHubPostalCode'        =>(string) optional($this->fromHub)->postal_code,
            'from_portal_code'        => $this->from_portal_code,

            'to_point_type'        => (string)$this->to_point_type,
            'toCityName'        => $this->toCity->name??'',
            'toHubCityName'        => $this->toHub->city->name??'',
            'toHubPostalCode'        => (string) optional($this->toHub)->postal_code,
            'to_portal_code'        => $this->to_portal_code,
            'dispatcher'            => $this->hub->name??'',
            'slots'                 => $this->slots??'',
            'total_distance'        => (string)$this->distance_km,
            'total_delivery_amount'        => (string)$this->total_delivery_amount,
            'special_discount'      =>(string) $this->special_discount,
            'liquid_fragile_amount'      =>(string) $this->liquid_fragile_amount,
            'rush_hour_amount'      =>(string) $this->rush_hour_amount,
            'scheduled_amount'      =>(string) $this->scheduled_amount,
            'packaging_amount'      =>(string) $this->packaging_amount,
            'delivery_charge'       => (string) ($this->delivery_charge+$this->special_discount),
            'delivery_fee'       => (string) ($this->total_delivery_amount - $this->cod_amount),
        ];
    }

}
