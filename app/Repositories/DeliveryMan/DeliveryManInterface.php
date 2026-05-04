<?php

namespace App\Repositories\DeliveryMan;

interface DeliveryManInterface
{

    public function all();
    public function get($id);
    public function filter($request);
    public function store($request);
    public function update($id, $request);
    public function delete($id);
    public function deliverymanEarn($type);
    public function totalCOD($type);
    public function paymentLogs();
    public function parcelPaymentLogs();
    public function all_country();
    public function all_city();
    public function all_district();
    public function all_town();
    public function accountStatus($id);
    public function verificationStatus($id);
    public function documentStatus($id);
}
