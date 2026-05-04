<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\Town\StoreRequest;
use App\Models\Backend\City;
use App\Repositories\Town\TownInterface;

class TownController extends Controller
{
    protected $repo;
    public function __construct(TownInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $towns = $this->repo->get();
        return view('backend.towns.index', compact('towns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = $this->repo->get_country();
        $cities = $this->repo->get_city();
        $districts = $this->repo->get_district();
        return view('backend.towns.create', compact('countries', 'cities', 'districts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        if ($this->repo->store($request)) :
            Toastr::success(__('town.added_msg'), __('message.success'));
            return redirect()->route('towns.index');
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
        $town = $this->repo->getFind($id);
        $countries = $this->repo->get_country();
        $cities = $this->repo->get_city();
        $districts = $this->repo->get_district();
        return view('backend.towns.edit', compact('town', 'countries', 'cities', 'districts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, string $id)
    {
        if ($this->repo->update($request, $id)) :
            Toastr::success(__('town.update_msg'), __('message.success'));
            return redirect()->route('towns.index');
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
            Toastr::success(__('town.delete_msg'), __('message.success'));
            return redirect()->back();
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        endif;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function cityByDistrict($id)
    {
        $city = $this->repo->cityByDistrict($id);
        return response()->json($city);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function districtByTown($id)
    {
        $towns = $this->repo->districtByTown($id);
        return response()->json($towns);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function townByPortalCode($id)
    {
        $town = $this->repo->townByPortalCode($id);
        return response()->json($town);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function cityWisePortalCode(Request $request)
    {
        $portal_code = '';
        if ($request->ajax()) {
            $portal_code = City::find($request->id)->portal_code;
        }
        return $portal_code;
    }
}
