<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\Country\StoreRequest;
use App\Repositories\Country\CountryInterface;

class CountryController extends Controller
{
    protected $repo;
    public function __construct(CountryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = $this->repo->get();
        return view('backend.countries.index', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.countries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        if ($this->repo->store($request)) :
            Toastr::success(__('country.added_msg'), __('message.success'));
            return redirect()->route('countries.index');
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
        $country = $this->repo->getFind($id);
        return view('backend.countries.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, string $id)
    {
        if ($this->repo->update($request, $id)) :
            Toastr::success(__('country.update_msg'), __('message.success'));
            return redirect()->route('countries.index');
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
            Toastr::success(__('country.delete_msg'), __('message.success'));
            return redirect()->back();
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        endif;
    }
}
