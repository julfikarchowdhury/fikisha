<?php

namespace App\Repositories\FrontWeb\WhyCourier;

use App\Models\Backend\FrontWeb\WhyCourier;
use App\Models\Backend\Upload;
use App\Repositories\FrontWeb\WhyCourier\WhyCourierInterface;
use Illuminate\Support\Facades\File;

class WhyCourierRepository implements WhyCourierInterface
{

    public function get()
    {
        return WhyCourier::orderBy('position', 'asc')->paginate(10);
    }
    public function getAll()
    {
        return WhyCourier::with('upload')->active()->orderBy('position', 'asc')->get();
    }
    public function getFind($id)
    {
        return WhyCourier::find($id);
    }
    public function store($request)
    {
        try {
            $whycourier              = new WhyCourier();
            $whycourier->icon        = $request->icon;
            $whycourier->content     = $request->content;
            $whycourier->position    = $request->position;
            $whycourier->status      = $request->status;
            $whycourier->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function update($id, $request)
    {
        try {
            $whycourier              = $this->getFind($id);
            $whycourier->content     = $request->content;
            $whycourier->icon        = $request->icon;
            $whycourier->position    = $request->position;
            $whycourier->status      = $request->status;
            $whycourier->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function delete($id)
    {
        return WhyCourier::destroy($id);
    }
}
