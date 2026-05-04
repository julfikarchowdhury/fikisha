<?php

namespace App\Http\Controllers\Backend;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\ParcelCategory\StoreRequest;
use App\Repositories\ParcelCategory\ParcelCategoryInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ParcelCategoryController extends Controller
{
    protected $repo;
    public function __construct(ParcelCategoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index(){
        $parcelCategories = $this->repo->all(null,[],10);
        return view('backend.parcel_category.index',compact('parcelCategories'));
    }
    public function create(){
        return view('backend.parcel_category.create');
    }

    public function store(StoreRequest $request){
        if($this->repo->store($request)):
            Toastr::success(__('levels.parcel_category_added_successfully'),__('message.success'));
            return redirect()->route('parcel.category.index');
        else:
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back()->withInput();
        endif;
    }
    public function edit($id){
        $parcelCategory  = $this->repo->getFind($id); 
        return view('backend.parcel_category.edit',compact('parcelCategory'));
    }
    public function update(StoreRequest $request){
        if($this->repo->update($request)):
            Toastr::success(__('levels.parcel_category_updated_successfully'),__('message.success'));
            return redirect()->route('parcel.category.index');
        else:
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back()->withInput();
        endif;
    }
    public function delete($id){
        if($this->repo->delete($id)):
            Toastr::success(__('levels.parcel_category_deleted_successfully'),__('message.success'));
            return redirect()->back();
        else:
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back();
        endif;
    }
}
