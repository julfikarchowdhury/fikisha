<?php

namespace App\Repositories\Country;

use App\Models\Backend\Country;
use App\Repositories\Country\CountryInterface;
use Illuminate\Support\Facades\DB;

class CountryRepository implements CountryInterface
{
    public function get()
    {
        return Country::orderBy('id', 'asc')->paginate(10);
    }

    public function getFind($id)
    {
        return Country::find($id);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $country                = new Country();
            $country->name          = $request->name;
            $country->code        = $request->code;
            $country->save();
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
            $country                = Country::find($id);
            $country->name          = $request->name;
            $country->code        = $request->code;
            $country->save();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function delete($id)
    {
        return Country::destroy($id);
    }
}
