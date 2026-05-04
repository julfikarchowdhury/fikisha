<?php

namespace App\Repositories\FrontWeb\Service;

use App\Enums\DeliveryType;
use App\Models\Backend\FrontWeb\Service;
use App\Models\Backend\Upload;
use App\Repositories\FrontWeb\Service\ServiceInterface;
use Illuminate\Support\Facades\File;

class ServiceRepository implements ServiceInterface
{

    public function get()
    {
        return Service::orderBy('position', 'asc')->paginate(10);
    }
    public function insideService()
    {
        return Service::where('delivery_type_id', DeliveryType::SAMEDAY)->orderBy('position', 'asc')->paginate(10);
    }
    public function outsideService()
    {
        return Service::where('delivery_type_id', DeliveryType::SUBCITY)->orderBy('position', 'asc')->paginate(10);
    }
    public function insideServiceAll()
    {
        return Service::where('delivery_type_id', DeliveryType::SAMEDAY)->active()->orderBy('position', 'asc')->get();
    }
    public function outsideServiceAll()
    {
        return Service::where('delivery_type_id', DeliveryType::SUBCITY)->active()->orderBy('position', 'asc')->get();
    }
    public function getAll()
    {
        return  Service::with('upload')->active()->orderBy('position', 'asc')->get();
    }
    public function getTakeService()
    {
        return  Service::with('upload')->active()->orderBy('position', 'asc')->take(4)->get();
    }
    public function latest_services()
    {
        return  Service::with('upload')->active()->orderBy('id', 'desc')->take(10)->get();
    }
    public function getFind($id)
    {
        return Service::find($id);
    }
    public function store($request)
    {
        try {
            $service                   = new Service();
            $service->delivery_type_id = $request->delivery_type_id;
            $service->shipping_type_id = $request->shipping_type_id;
            if ($request->image):
                $service->image_id    = $this->imageStoreUpdate($request->image, '');
            endif;
            $service->description = $request->description;
            $service->position    = $request->position;
            $service->status      = $request->status;
            $service->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function update($id, $request)
    {
        try {
            $service                   = $this->getFind($id);
            $service->delivery_type_id = $request->delivery_type_id;
            $service->shipping_type_id = $request->shipping_type_id;
            if ($request->image):
                $service->image_id     = $this->imageStoreUpdate($request->image, $service->image_id);
            endif;
            $service->description = $request->description;
            $service->position    = $request->position;
            $service->status      = $request->status;
            $service->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function delete($id)
    {
        return Service::destroy($id);
    }

    // Image Store in Upload Model 
    public function imageStoreUpdate($file, $file_id = '')
    {

        try {
            $file_name = '';
            if (!blank($file)) {
                if (!File::exists(public_path('uploads/service'))):
                    File::makeDirectory(public_path('uploads/service'));
                endif;
                $destinationPath       = public_path('uploads/service');
                $img          = date('YmdHis') . "." . $file->getClientOriginalExtension();
                $file->move($destinationPath, $img);
                $file_name            = 'uploads/service/' . $img;
            }

            if (blank($file_id)) {
                $upload           = new Upload();
            } else {
                $upload           = Upload::find($file_id);
                if (file_exists(public_path($upload->original))) {
                    unlink(public_path($upload->original));
                }
            }
            $upload->original     = $file_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return null;
        }
    }
}
