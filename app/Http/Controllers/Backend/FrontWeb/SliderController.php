<?php

namespace App\Http\Controllers\Backend\FrontWeb;

use App\Http\Controllers\Controller;
use App\Http\Requests\FrontWeb\Slider\StoreRequest;
use App\Http\Requests\FrontWeb\Slider\UpdateRequest;
use App\Repositories\FrontWeb\Slider\SliderInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $repo;
    public function __construct(SliderInterface $repo)
    {
        $this->repo = $repo;
    }
    public function index(Request $request)
    {
        $sliders       = $this->repo->get();

        return view('backend.front_web.slider.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('backend.front_web.slider.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreRequest $request)
    {
         
        if ($this->repo->store($request)) :
            Toastr::success(__('levels.slider_added_successfully'), __('message.success'));
            return redirect()->route('slider.index');
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        endif;
    }



    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $singleSlider = $this->repo->getFind($id);
        return view('backend.front_web.slider.edit', compact('singleSlider'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdateRequest $request)
    {
        

        if ($this->repo->update($request->id, $request)) :
            Toastr::success(__('levels.slider_updated_successfully'), __('message.success'));
            return redirect()->route('slider.index');
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        endif;
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function delete($id)
    {
       
        if ($this->repo->delete($id)) :

            Toastr::success(__('levels.slider_deleted_successfully'), __('message.success'));
            return redirect()->back();
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        endif;
    }
}
