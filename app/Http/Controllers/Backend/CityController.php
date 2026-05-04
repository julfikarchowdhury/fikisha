<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\City\StoreMultipleRequest;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\City\StoreRequest;
use App\Models\Backend\City;
use App\Models\Backend\Province;
use App\Repositories\City\CityInterface;
use App\Repositories\Province\ProvinceInterface;

class CityController extends Controller
{
    protected $repo;
    protected $provinceRepo;
    public function __construct(
        CityInterface $repo,
        ProvinceInterface $provinceRepo
    ) {
        $this->repo = $repo;
        $this->provinceRepo = $provinceRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = $this->repo->get();
        return view('backend.cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $provinces       = $this->provinceRepo->getActive();
        return view('backend.cities.create', compact('provinces'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function addMultipleCity()
    {
        $provinces       = $this->provinceRepo->getActive();
        return view('backend.cities.add_multiple', compact('provinces'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        if ($this->repo->store($request)) :
            Toastr::success(__('city.added_msg'), __('message.success'));
            return redirect()->route('cities.index');
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput();
        endif;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function multipleCityStore(StoreMultipleRequest $request)
    {
        $province = Province::find($request->province_id);
        $province->province_code = $request->province_code;
        $province->save();
        if (count($request->name) > 0) {
            foreach ($request->name as $key => $value) {
                City::create([
                    'province_id'   => $request->province_id,
                    'name'          => $request->name[$key],
                    'portal_code'   => $request->portal_code[$key]
                ]);
            }
        }
        Toastr::success(__('city.added_msg'), __('message.success'));
        return redirect()->route('cities.index');
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
        $city           = $this->repo->getFind($id);
        $provinces      = $this->provinceRepo->getActive();
        return view('backend.cities.edit', compact('city', 'provinces'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, string $id)
    {
        if ($this->repo->update($request, $id)) :
            Toastr::success(__('city.update_msg'), __('message.success'));
            return redirect()->route('cities.index');
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
            Toastr::success(__('city.delete_msg'), __('message.success'));
            return redirect()->back();
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        endif;
    }
}
