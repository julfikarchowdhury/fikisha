<?php

namespace App\Repositories\Merchant;

interface MerchantInterface
{
    public function all();
    public function gatAll();
    public function merchantIdlist();
    public function all_country();
    public function all_city();
    public function all_district();
    public function all_town();
    public function get($id);
    public function store($request);
    public function signUpStore($request);
    public function merchantDocumentSubmit($request);
    public function accountStatus($id);
    public function verificationStatus($id);
    public function documentStatus($id);
    public function otpVerification($request);
    public function resendOTP($request);
    public function update($id, $request);
    public function delete($id);
    public function merchant_shops_get($id);
    public function socialSignupStore($request, $social);
    public function countryByCity($id);
    public function cityByDistrict($id);
    public function districtByTown($id);
    public function townByPortalCode($id);
    public function townByCity($id);
    public function toCountries($request);
    public function toCities($request);
    public function toDistrict($request);
    public function toTown($request);
    public function toPortalCode($request);
}
