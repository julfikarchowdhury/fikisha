<?php

namespace App\Repositories\City;

use App\Models\Backend\City;
use App\Models\Backend\Country;
use App\Repositories\City\CityInterface;

class CityRepository implements CityInterface
{
    public function get()
    {
        return City::with('country')->orderBy('id', 'asc')->paginate(10);
    }

    public function getFind($id)
    {
        return City::find($id);
    }

    public function store($request)
    {
        try {
            $city                   = new City();
            $city->province_id      = $request->province_id;
            $city->name             = $request->name;
            $city->portal_code      = $request->portal_code;
            $city->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function update($request, $id)
    {
        try {
            $city                       = City::find($id);
            $city->province_id          = $request->province_id;
            $city->name                 = $request->name;
            $city->portal_code          = $request->portal_code;
            $city->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function delete($id)
    {
        return City::destroy($id);
    }

    public function get_country()
    {
        return Country::all();
    }
}
