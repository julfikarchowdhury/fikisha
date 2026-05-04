<?php
namespace App\Repositories\ShippingType;

interface ShippingTypeInterface {

    public function all();
    public function getAll();
    public function get($id);
    public function store($request);
    public function update($request);
    public function delete($id);
    public function getActive($request);
    public function getActiveAll();

    public function insideShippingTypes();
    public function outsideShippingTypes();
}
