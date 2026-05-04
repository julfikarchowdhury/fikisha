<?php

namespace App\Http\Controllers\Api\V10;

use App\Enums\ParcelStatus;
use App\Enums\StatementType;
use App\Enums\InvoiceStatus;
use App\Enums\WhoPays;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateRequest;
use App\Http\Resources\v10\Collection\ParcelCollection;
use App\Http\Resources\v10\DeliverymanUserResource;
use App\Http\Resources\v10\ParcelLogsResource;
use App\Http\Resources\v10\ParcelResource;
use App\Repositories\DeliveryMan\DeliveryManInterface;
use App\Repositories\Parcel\ParcelInterface;
use App\Traits\ApiReturnFormatTrait;
use App\Models\Backend\Parcel;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DeliverymanOrderController extends Controller
{
    use ApiReturnFormatTrait;

    protected $parcel;
    protected $deliveryman;
    public function __construct(ParcelInterface $parcel, DeliveryManInterface $deliveryman)
    {
        $this->parcel  = $parcel;
        $this->deliveryman = $deliveryman;
    }

    public function orderList(Request $request){
        try {
            $request->merge(['limit'=>$request->per_page]);
            $data         = [];
            $data['deliveryman_assign']      = new ParcelCollection($this->parcel->deliverymanAssignedParcel($request,optional(auth()->user()->deliveryman)->id));
            $data['failuredParcel']       = new ParcelCollection($this->parcel->deliverymanFailuredParcel($request,optional(auth()->user()->deliveryman)->id));
            $data['delivered']               = new ParcelCollection($this->parcel->deliverymanDeliveredParcel($request,optional(auth()->user()->deliveryman)->id));

            return $this->responseWithSuccess(__('dashboard.delivery_man'),$data,200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('parcel.error_msg'), [],500);
        }
    }
    public function orderSearch(Request $request){
        try {
            $request->merge(['limit'=>$request->per_page]);
            $data         = [];
            $data['orderParcel']              = new ParcelCollection($this->parcel->searchOrder($request,optional(auth()->user()->deliveryman)->id));

            return $this->responseWithSuccess(__('dashboard.delivery_man'),$data,200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('parcel.error_msg'), [],500);
        }
    }
    public function orderDeliverymanAssign(Request $request){
        try {

           $data         = [];
           $request->merge(['delivery_man_id'=>optional(auth()->user()->deliveryman)->id]);
           $order =  $this->parcel->orderDeliverymanAssign($request,optional(auth()->user()->deliveryman)->id);
            return $this->responseWithSuccess($order['message'],$data,200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('parcel.error_msg'), [],500);
        }
    }



    public function details($id)
    {

        try {
            $parcel       = $this->repo->details($id);
            $parcelEvents = $this->repo->parcelEvents($id);
            return $this->responseWithSuccess(__('parcel.parcel_details'), ['parcel'=>ParcelResource::collection($parcel),'parcelEvents'=>ParcelLogsResource::collection($parcelEvents)], 200);
        }catch (\Exception $exception){
            return $this->responseWithError(__('parcel.parcel_details'), [], 500);

        }
    }

    public function orderStatus(Request $request){
        try {
            $data         = [];
            $parcel  = $this->parcel->details($request->parcelID);
            $data['parcelStatusDriver'] = parcelStatusDriver($parcel);
            return $this->responseWithSuccess(__('parcel.status'),$data,200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('parcel.error_msg'), [],500);
        }
    }


    public function parcelStatusUpdate(Request $request){
        switch ($request->status) {
            //return to courier
            case ParcelStatus::RETURN_TO_COURIER:
                if($this->parcel->returntoQourier($request->parcel_id, $request)):
                    return $this->responseWithSuccess(__('parcel.return_to_qourier_success'),[],200);
                else:
                    return $this->responseWithError(__('parcel.error_msg'), [],500);
                endif;
                break;

            //partial delivered
            case ParcelStatus::PARTIAL_DELIVERED:
                if($this->parcel->parcelPartialDelivered($request->parcel_id, $request)):
                    return $this->responseWithSuccess(__('parcel.partial_delivered_success'),[],200);
                else:
                    return $this->responseWithError(__('parcel.error_msg'), [],500);
                endif;
                break;

            //delivered
            case ParcelStatus::DELIVERED:
                $parcel = Parcel::find($request->parcel_id);
                if ($parcel) {
                    $paymentStatus = (int) ($parcel->payment_status ?? 0);
                    if ($paymentStatus !== InvoiceStatus::PAID) {
                        return $this->responseWithError('Payment is unpaid. Delivery cannot be completed.', [], 409);
                    }
                }
                if($this->parcel->parcelDelivered($request->parcel_id, $request)):
                    return $this->responseWithSuccess(__('parcel.delivered_success'),[],200);
                else:
                    return $this->responseWithError(__('parcel.error_msg'), [],500);
                endif;
                break;
            default:
                if($this->parcel->ParcelStatusUpdate($request->parcel_id,$request->status, $request)):
                    return $this->responseWithSuccess(__('parcel.status'),[],200);
                else:
                    return $this->responseWithError(__('parcel.error_msg'), [],500);
                endif;
                break;

        }
    }

    //profile
    public function profile(){
        try {

            $data                         = [];
            $data['user']                 = new  DeliverymanUserResource(Auth::user());
            $data['current_balance']      = $data['user']->deliveryman->current_balance;
            $data['deliveryman_earn']     = $this->deliveryman->deliverymanEarn(StatementType::INCOME)->sum('amount');
            $data['total_cod']            = number_format($this->deliveryman->totalCOD(StatementType::EXPENSE)->sum('amount') - $this->deliveryman->totalCOD(StatementType::INCOME)->sum('amount'),2) ;
            $data['delivery_in_progress'] = $this->parcel->deliverymanStatusParcel(request(),ParcelStatus::DELIVERY_MAN_ASSIGN,null)->count();
            $data['completed_delivered']  = $this->parcel->deliverymanStatusParcel(request(),ParcelStatus::DELIVERED,null)->count();
            $data['canceled_delivered']   = $this->deliveryman->totalCOD(StatementType::INCOME)->groupBy('parcel_id')->count();

            return $this->responseWithSuccess(__('dashboard.delivery_man'),$data,200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('parcel.error_msg'), [],500);
        }
    }
    public function profileUpdate(Request $request){
        $validator = new UpdateRequest();
        $validator = Validator::make($request->all(), $validator->rules());

        if ($validator->fails()) {
            return $this->responseWithError(__('auth.profile_update'), ['message' => $validator->errors()], 422);
        }
        if($this->profileRep->update(auth()->user()->id, $request)){
            return $this->responseWithSuccess(__('auth.profile_update'), [], 200);
        }else{
            return $this->responseWithError(__('auth.error_msg'), [], 500);
        }
    }


    public function paymentLogs(){
        try {

            $data = [];
            $data['income'] = $this->deliveryman->paymentLogs()['income'];
            $data['expense'] = $this->deliveryman->paymentLogs()['expense'];
            return $this->responseWithSuccess(__('dashboard.delivery_man'),$data,200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('parcel.error_msg'), [],500);
        }
    }

    public function parcelPaymentLogs(){
        try {

            $data = [];
            $data['parcel_payment_logs'] = $this->deliveryman->parcelPaymentLogs();
            return $this->responseWithSuccess(__('dashboard.delivery_man'),$data,200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('parcel.error_msg'), [],500);
        }
    }


    public function parcelStatus(){
        try {
            $data    = [];
            $data [] = trans('ApiParcelStatus');

            return $this->responseWithSuccess(__('parcel.status'),$data,200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('parcel.error_msg'), [],500);
        }
    }

}
