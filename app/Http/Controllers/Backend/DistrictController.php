<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\District\StoreRequest;
use App\Repositories\District\DistrictInterface;

class DistrictController extends Controller
{
    protected $repo;
    public function __construct(DistrictInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $districts = $this->repo->get();
        return view('backend.districts.index', compact('districts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = $this->repo->get_country();
        $cities = $this->repo->get_city();
        return view('backend.districts.create', compact('countries', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        if ($this->repo->store($request)) :
            Toastr::success(__('district.added_msg'), __('message.success'));
            return redirect()->route('districts.index');
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput();
        endif;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $district = $this->repo->getFind($id);
        $countries = $this->repo->get_country();
        $cities = $this->repo->get_city();
        return view('backend.districts.edit', compact('district', 'countries', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, string $id)
    {
        if ($this->repo->update($request, $id)) :
            Toastr::success(__('district.update_msg'), __('message.success'));
            return redirect()->route('districts.index');
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput();
        endif;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($this->repo->delete($id)) :
            Toastr::success(__('district.delete_msg'), __('message.success'));
            return redirect()->back();
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        endif;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function countryByCity($id)
    {
        $city = $this->repo->countryByCity($id);
        return response()->json($city);
    }
}
