<?php

namespace App\Repositories\ShippingType;

use App\Enums\Status;
use App\Models\Backend\ShippingType;
use App\Repositories\ShippingType\ShippingTypeInterface;
use App\Models\Backend\ShippingChargeOption;

class ShippingTypeRepository implements ShippingTypeInterface
{
    protected $model;
    public function __construct(ShippingType $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model::orderBy('id', 'desc')->paginate(10);
    }

    public function getAll()
    {
        return $this->model::all();
    }

    public function get($id)
    {
        return $this->model::with('ShippingChargeOptions')->find($id);
    }

    public function insideShippingTypes()
    {
        return $this->model::where('delivery_type_id', 1)->paginate(10); // 1 = inside city
    }

    public function outsideShippingTypes()
    {
        return $this->model::where('delivery_type_id', 3)->paginate(10); // 3 = outside city
    }

    public function getActiveAll()
    {
        return $this->model::orderBy('id', 'desc')->paginate(10);
    }

    public function getActive($request)
    {
        return $this->model::where('delivery_type_id', $request->delivery_type_id)
            ->orderBy('id', 'asc')
            ->get();
    }

    public function store($request)
    {
        try {
            ShippingType::create($request->all());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update($request)
    {
        try {
            $id = $request->id;
            $requestData = $request->except(['_token', 'id', '_method', 'options', 'delivery_type_id']);
            ShippingType::where('id', $id)->update($requestData);
            if ($request->delivery_type_id == 3) :
                $this->outsideCityOptions($request, $id);
            endif;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function outsideCityOptions($request, $id)
    {
        try {
            if ($request->options) :
                ShippingChargeOption::where('shipping_type_id', $id)->delete();
                foreach ($request->options as $key =>  $option) {
                    $option  = (object)$option;
                    ShippingChargeOption::create([
                        'shipping_type_id' => $id,
                        'from_km'          => $option->from_km,
                        'to_km'            => $option->to_km,
                        'basic_price'      => $option->basic_price
                    ]);
                }
            endif;
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function delete($id)
    {
        return $this->model::destroy($id);
    }
}
