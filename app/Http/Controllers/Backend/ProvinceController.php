<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Province\StoreRequest;
use App\Repositories\Province\ProvinceInterface;
use App\Traits\BulkValidatorRequestTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{

    use BulkValidatorRequestTrait;
    protected $repo; 
    public function __construct( 
        ProvinceInterface $repo
    ) {
        $this->repo = $repo; 
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $provinces = $this->repo->get();
        return view('backend.provinces.index', compact('provinces'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    { 
        return view('backend.provinces.create');
    }
  
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request['provinces'] = $this->ProvinceRequest($request);
        if($request->provinces == null): 
            return response()->json(['success'=>null],200);
        elseif ($this->repo->store($request)) :
            return response()->json(['success'=>true],200);
        else:
            return response()->json(['success'=>false],200);
        endif;
    }

     
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $province           = $this->repo->getFind($id); 
        return view('backend.provinces.edit', compact( 'province'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request)
    {
        if ($this->repo->update($request, $request->id)) :
            Toastr::success(__('levels.province_update_msg'), __('message.success'));
            return redirect()->route('province.index');
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back()->withInput();
        endif;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        if ($this->repo->delete($id)) :
            Toastr::success(__('levels.province_delete_msg'), __('message.success'));
            return redirect()->back();
        else :
            Toastr::error(__('parcel.error_msg'), __('message.error'));
            return redirect()->back();
        endif;
    }
}
