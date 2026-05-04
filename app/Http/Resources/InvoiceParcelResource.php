<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceParcelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
                'updated_at'        =>  Carbon::parse($this->parcel->update_at)->format('d-m-Y'),
                'customer_info'     => $this->parcel->customer_name.','.$this->parcel->customer_phone.','.$this->parcel->customer_address, 
                'tracking_id'       => $this->parcel->tracking_id, 
                'status'            => __('parcelStatus.'.$this->parcel_status), 
                'total_charge'      => $this->total_shipping_fee,
                'current_payable'   => $this->current_payable,
            ];
    }
}
