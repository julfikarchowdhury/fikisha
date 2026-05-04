<?php

namespace App\Repositories\Vehicle;

use App\Models\Backend\Vehicle;
use App\Repositories\Vehicle\VehicleInterface;

class VehicleRepository implements VehicleInterface
{
    public function all()
    {
        return Vehicle::orderBy('name', 'asc')->paginate(10);
    }

    public function getAll()
    {
        return Vehicle::orderBy('name', 'asc')->get();
    }

    public function get($id)
    {
        return Vehicle::find($id);
    }

    public function store($request)
    {
        try {
            $vehicle                        = new Vehicle();
            $vehicle->name                  = $request->name;
            $vehicle->hub_id                = $request->hub_id;
            $vehicle->description           = $request->description;
            $vehicle->registration_number   = $request->registration_number;
            $vehicle->capacity              = $request->capacity;
            $vehicle->size                  = $request->size;
            $vehicle->status                = $request->status;
            $vehicle->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update($request)
    {
        try {
            $vehicle                        = Vehicle::find($request->id);
            $vehicle->name                  = $request->name;
            $vehicle->hub_id                = $request->hub_id;
            $vehicle->description           = $request->description;
            $vehicle->registration_number   = $request->registration_number;
            $vehicle->capacity              = $request->capacity;
            $vehicle->size                  = $request->size;
            $vehicle->status                = $request->status;
            $vehicle->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete($id)
    {
        return Vehicle::destroy($id);
    }
}
