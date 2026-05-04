<?php

namespace App\Repositories\District;

use App\Models\Backend\City;
use App\Models\Backend\District;
use App\Models\Backend\Country;
use App\Repositories\District\DistrictInterface;
use Illuminate\Support\Facades\DB;

class DistrictRepository implements DistrictInterface
{
    public function get()
    {
        return District::with('country')->orderBy('id', 'asc')->paginate(10);
    }

    public function getFind($id)
    {
        return District::find($id);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $district                = new District();
            $district->city_id        = $request->city_id;
            $district->name          = $request->name;
            $district->save();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $district                = District::find($id);
            $district->city_id        = $request->city_id;
            $district->name          = $request->name;
            $district->save();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function delete($id)
    {
        return District::destroy($id);
    }

    public function get_country()
    {
        return Country::all();
    }

    public function get_city()
    {
        return City::all();
    }

    public function countryByCity($id)
    {
        return City::where('country_id', $id)->get();
    }
}
