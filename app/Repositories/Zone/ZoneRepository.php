<?php

namespace App\Repositories\Zone;

use App\Enums\Status;
use App\Models\Zone;
use Carbon\Carbon;
use App\Repositories\Zone\ZoneInterface;

class ZoneRepository implements ZoneInterface
{
    public function getAllActive()
    {
        return Zone::where('status', Status::ACTIVE)->orderBy('id', 'asc')->get();
    }
    public function get()
    {
        return Zone::orderByDesc('id')->paginate(10);
    }
    public function getFind($id)
    {
        return Zone::find($id);
    }
    public function store($request)
    {
        try {
            $zoneRate  = Zone::create([
                'name'               => $request->name,
                'position'           => $request->position,
                'status'             => $request->status
            ]);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function update($request)
    {
        try {
            $zoneRate  = Zone::where('id', $request->id)->update([
                'name'               => $request->name,
                'position'           => $request->position,
                'status'             => $request->status
            ]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function delete($id)
    {
        return Zone::destroy($id);
    }
}
