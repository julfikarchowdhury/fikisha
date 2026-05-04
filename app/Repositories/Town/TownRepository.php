<?php

namespace App\Repositories\Town;

use App\Models\Backend\City;
use App\Models\Backend\District;
use App\Models\Backend\Country;
use App\Models\Backend\Town;
use App\Repositories\Town\TownInterface;
use Illuminate\Support\Facades\DB;

class TownRepository implements TownInterface
{
    public function get()
    {
        return Town::with('country')->orderBy('id', 'asc')->paginate(10);
    }

    public function getAll()
    {
        return Town::orderBy('id', 'asc')->get();
    }

    public function getFind($id)
    {
        return Town::find($id);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $town                = new Town();
            $town->city_id        = $request->city_id;
            $town->district_id        = $request->district_id;
            $town->name          = $request->name;
            $town->portal_code          = $request->portal_code;
            $town->save();
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
            $town                = Town::find($id);
            $town->city_id        = $request->city_id;
            $town->district_id        = $request->district_id;
            $town->name          = $request->name;
            $town->portal_code          = $request->portal_code;
            $town->save();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function delete($id)
    {
        return Town::destroy($id);
    }

    public function get_country()
    {
        return Country::all();
    }

    public function get_city()
    {
        return City::all();
    }

    public function get_district()
    {
        return District::all();
    }

    public function cityByDistrict($id)
    {
        return District::where('city_id', $id)->get();
    }

    public function districtByTown($id)
    {
        return Town::where('district_id', $id)->get();
    }

    public function townByPortalCode($id)
    {
        return Town::find($id);
    }
}
