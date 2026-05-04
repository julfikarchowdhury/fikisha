<?php
namespace App\Repositories\Vehicle;

interface VehicleInterface {

    public function all();
    public function getAll();
    public function get($id);
    public function store($request);
    public function update($request);
    public function delete($id);

}
