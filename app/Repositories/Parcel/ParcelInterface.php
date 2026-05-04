<?php

namespace App\Repositories\Parcel;

interface ParcelInterface
{

    public function all();
    public function parcelReturnReport($request);
    public function deliveryManParcel();
    public function filter($request);
    public function get($id);
    public function parcelEvents($id);
    public function parcelTracking($request);
    public function parcelTrackingQr($tracking_id);
    public function details($id);
    public function statusUpdate($id, $status_id);
    public function ParcelStatusUpdate($id, $status_id, $request);

    public function ParcelCancel($request);
    public function store($request);
    public function storeApi($request);
    public function calculateTheQuote($request);
    public function parcelItemCalculate($request);
    public function duplicateStore($request);
    public function update($id, $request);
    public function delete($id);
    public function deliveryCharges();
    public function deliveryCategories();
    public function packaging();
    public function deliveryTypes();
    public function pickupdatemanAssigned($id, $request);
    public function pickupdatemanAssignedCancel($id, $request);
    public function readyToReassign($id, $request);
    public function readyToReassignBooking($id, $request);
    public function confirmedBooking($id, $request);
    public function orderProcessing($id, $request);
    public function PickupReSchedule($id, $request);
    public function PickupReScheduleCancel($id, $request);
    public function receivedBypickupman($id, $request);
    public function receivedBypickupmanCancel($id, $request);
    public function receivedWarehouse($id, $request);
    public function receivedWarehouseCancel($id, $request);
    public function deliveryManAssignMultipleParcel($request);
    public function deliverymanAssign($id, $request);
    public function deliverymanAssignCancel($id, $request);
    public function deliveryReschedule($id, $request);
    public function deliveryReScheduleCancel($id, $request);
    public function returntoQourier($id, $request);
    public function returntoQourierCancel($id, $request);
    public function returnAssignToMerchant($id, $request);
    public function returnAssignToMerchantCancel($id, $request);
    public function returnAssignToMerchantReschedule($id, $request);
    public function returnAssignToMerchantRescheduleCancel($id, $request);
    public function returnReceivedByMerchant($id, $request);
    public function returnReceivedByMerchantCancel($id, $request);
    public function parcelDelivered($id, $request);
    public function parcelDeliveredCancel($id, $request);
    public function parcelPartialDelivered($id, $request);
    public function parcelPartialDeliveredCancel($id, $request);
    public function search($data);
    public function searchDeliveryManAssingMultipleParcel($data);
    public function searchExpense($data);
    public function searchIncome($data);
    public function pickupdatemanAssignedBulk($request);
    public function AssignReturnToMerchantBulk($request);
    public function bulkParcels($ids);
    public function deliverymanStatusParcel($request,$status,$deliverymanID);
    public function filterPrint($request);
    public function ParcelSearch($request);
    public function parcelMultiplePrintLabel($request);
}
