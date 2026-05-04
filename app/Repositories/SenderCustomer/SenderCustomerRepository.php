<?php

namespace App\Repositories\SenderCustomer;

use App\Models\Backend\SenderCustomer;
use App\Repositories\SenderCustomer\SenderCustomerInterface;

class SenderCustomerRepository implements SenderCustomerInterface
{
    public function all()
    {
        return SenderCustomer::orderBy('name', 'asc')->paginate(10);
    }

    public function senderCustomer($sender_id)
    {
        return SenderCustomer::where('sender_id', $sender_id)->orderBy('id', 'asc')->paginate(10);
    }

    public function getAll()
    {
        return SenderCustomer::orderBy('name', 'asc')->get();
    }

    public function get($id)
    {
        return SenderCustomer::find($id);
    }

    public function store($request)
    {
        try {
            $sender_customer                    = new SenderCustomer();
            $sender_customer->sender_id         = $request->sender_id;
            $sender_customer->province_id       = $request->province_id;
            $sender_customer->city_id           = $request->city_id;
            $sender_customer->account_type      = $request->account_type;
            $sender_customer->first_name        = $request->first_name;
            $sender_customer->last_name         = $request->last_name;
            $sender_customer->phone_number      = $request->phone_number;
            $sender_customer->country_code      = $request->country_code;
            $sender_customer->email             = $request->email;
            $sender_customer->whatsapp_number   = $request->whatsapp_number;
            $sender_customer->address           = $request->address;
            $sender_customer->status            = $request->status;
            $sender_customer->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update($request, $id)
    {
        try {
            $sender_customer                    = SenderCustomer::find($id);
            $sender_customer->sender_id         = $request->sender_id;
            $sender_customer->province_id       = $request->province_id;
            $sender_customer->city_id           = $request->city_id;
            $sender_customer->account_type      = $request->account_type;
            $sender_customer->first_name        = $request->first_name;
            $sender_customer->last_name         = $request->last_name;
            $sender_customer->phone_number      = $request->phone_number;
            $sender_customer->country_code      = $request->country_code;
            $sender_customer->email             = $request->email;
            $sender_customer->whatsapp_number   = $request->whatsapp_number;
            $sender_customer->address           = $request->address;
            $sender_customer->status            = $request->status;
            $sender_customer->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete($id)
    {
        return SenderCustomer::destroy($id);
    }
}
