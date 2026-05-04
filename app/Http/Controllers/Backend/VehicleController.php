<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\StoreRequest;
use App\Http\Requests\Vehicle\UpdateRequest;
use App\Repositories\Vehicle\VehicleInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class VehicleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $repo;

    public function __construct(VehicleInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $vehicles = $this->repo->all();
        return view('backend.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        if ($this->repo->store($request)) {
            Toastr::success('Vehicles successfully added.', __('message.success'));
            return redirect()->route('vehicles.index');
        } else {
            Toastr::error('Something went wrong.', __('message.error'));
            return redirect()->back()->withInput();
        }
    }

   
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vehicle             = $this->repo->get($id);
        return view('backend.vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request)
    {
        if ($this->repo->update($request)) {
            Toastr::success('Vehicle successfully Update.', __('message.success'));
            return redirect()->route('vehicles.index');
        } else {
            Toastr::error('Something went wrong.', __('message.success'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repo->delete($id);
        Toastr::success('Asset successfully deleted.', __('message.success'));
        return back();
    }
}
