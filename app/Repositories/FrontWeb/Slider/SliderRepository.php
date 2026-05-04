<?php

namespace App\Repositories\FrontWeb\Slider;

use App\Enums\Status;
use App\Models\Backend\FrontWeb\Slider;
use App\Models\Backend\Upload;
use App\Repositories\FrontWeb\Slider\SliderInterface;

class SliderRepository implements SliderInterface
{

    public function all()
    {
        return Slider::with('upload')->where('status', Status::ACTIVE)->orderBy('position', 'asc')->get();
    }

    public function get()
    {
        return Slider::with('upload')->orderBy('position', 'asc')->paginate(10);
    }

    public function getFind($id)
    {
        return Slider::find($id);
    }

    public function store($request)
    {
        try {
            $slider                    = new Slider();
            $slider->title             = $request->title;
            $slider->slider            = $this->file($request->slider, '');
            $slider->small_title       = $request->small_title;
            $slider->position          = $request->position;
            $slider->status            = $request->status;
            $slider->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function update($id, $request)
    {
        try {
            $slider                    = Slider::find($id);
            $slider->title             = $request->title;
            if ($request->slider) :
                $slider->slider            = $this->file($request->slider, $slider->slider);
            endif;
            $slider->small_title       = $request->small_title;
            $slider->position          = $request->position;
            $slider->status            = $request->status;
            $slider->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $sliderFind = Slider::find($id);
            // $this->upload->unlinkImage($sliderFind->slider);
            $sliderFind->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    // Request image Store in Upload Model and image copy file attach in public/upload/user folder.
    public function file($image, $image_id = '')
    {
        try {
            $image_name = '';
            if (!blank($image)) {
                $destinationPath       = public_path('uploads/users');
                $profileImage          = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
                $image_name            = 'uploads/users/' . $profileImage;
            }
            if (blank($image_id)) {
                $upload           = new Upload();
            } else {
                $upload           = Upload::find($image_id);
                if (file_exists(public_path($upload->original))) {
                    unlink(public_path($upload->original));
                }
            }
            $upload->original     = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }
}
