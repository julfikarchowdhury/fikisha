<?php

namespace App\Repositories\Province;

use App\Enums\Status;
use App\Models\Backend\Province;
use App\Repositories\Province\ProvinceInterface;
use Illuminate\Support\Facades\DB;

class ProvinceRepository implements ProvinceInterface
{
    public function get()
    {
        return Province::orderBy('position', 'asc')->paginate(10);
    }

    public function getActive()
    {
        return Province::where('status', Status::ACTIVE)->orderBy('position', 'asc')->get();
    }

    public function getFind($id)
    {
        return Province::find($id);
    }

    public function store($request)
    {

        DB::beginTransaction();
        try {
            foreach ($request->provinces as  $singleProvinceData) {
                $singleProvince                   = (object) $singleProvinceData;
                $province                   = new Province();
                $province->name             = $singleProvince->name;
                $province->province_code    = $singleProvince->province_code;
                $province->position         = $singleProvince->position;
                $province->description      = $singleProvince->description;
                $province->save();
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function update($request)
    {
        DB::beginTransaction();
        try {
            $province                   = Province::find($request->id);
            $province->name             = $request->name;
            $province->province_code    = $request->province_code;
            $province->position         = $request->position;
            $province->description      = $request->description;
            $province->save();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function delete($id)
    {
        return Province::destroy($id);
    }
}
