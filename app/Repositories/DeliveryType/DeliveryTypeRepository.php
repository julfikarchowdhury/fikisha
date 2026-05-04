<?php
namespace App\Repositories\DeliveryType;

use App\Enums\Status;
use App\Models\Backend\DeliveryType;
use App\Repositories\DeliveryType\DeliveryTypeInterface;
class DeliveryTypeRepository implements DeliveryTypeInterface{

    protected $model;
    public function __construct(DeliveryType $model)
    {
        $this->model = $model;
    }
    public function get(){
        return $this->model::orderBy('position')->paginate(10);
    }
    public function getActive(){
        return $this->model::active()->orderBy('position')->get();
    }

    public function getFind($id){
        return $this->model::find($id);
    }

    public function store($request){
        try {
            $request =  $request->except(['_method','_token']);
          
            $this->model::create($request);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function update($request){
        try {
            $requestdata =  $request->except(['_method','_token']);
            $this->model::where('id',$request->id)->update($requestdata);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function delete($id){
        return $this->model::destroy($id);
    }
}
