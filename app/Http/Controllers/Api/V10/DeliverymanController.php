<?php

namespace App\Http\Controllers\Api\V10;

use App\Enums\ParcelStatus;
use App\Enums\StatementType;
use App\Enums\InvoiceStatus;
use App\Enums\WhoPays;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateRequest;
use App\Http\Resources\v10\DeliverymanUserResource;
use App\Http\Resources\v10\ParcelResource;
use App\Http\Resources\v10\UserResource;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Parcel;
use App\Models\User;
use App\Repositories\DeliveryMan\DeliveryManInterface;
use App\Repositories\Parcel\ParcelInterface;
use App\Repositories\Profile\ProfileRepository;
use App\Traits\ApiReturnFormatTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DeliverymanController extends Controller
{
    use ApiReturnFormatTrait;

    protected $parcel;
    protected $deliveryman;
    protected $profileRep;
    public function __construct(ParcelInterface $parcel, DeliveryManInterface $deliveryman, ProfileRepository $profileRep)
    {
        $this->parcel      = $parcel;
        $this->deliveryman = $deliveryman;
        $this->profileRep  = $profileRep;
    }

    //dashboard
    public function dashboard(){
        try {

            $data         = [];
           $deliverymanId =  optional(auth()->user()->deliveryman)->id;
            $data['totalLoad'] = 0;
            $data['totalLoadItem'] = 0;
            $data['totalBatchUnload'] = 0;

            // Define the statuses and labels for grouping counts
            $statuses = [
                'delivered' => [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED],
                'delivery_man_assign' => [ParcelStatus::DELIVERED, ParcelStatus::PARTIAL_DELIVERED, ParcelStatus::DELIVERY_FAILED, ParcelStatus::DELIVERY_FAILURE],
                'pickup_man_assign' => [ParcelStatus::PICKUP_RE_SCHEDULE, ParcelStatus::PICKUP_ASSIGN],
                'return_to_courier' => [ParcelStatus::RETURN_TO_COURIER],
                'total_failure' => [ ParcelStatus::DELIVERY_FAILURE,
                    ParcelStatus::DELIVERY_FAILED,
                    ParcelStatus::PARCEL_CANCEL,
                    ParcelStatus::DELIVERY_ATTEMPT1,
                    ParcelStatus::DELIVERY_ATTEMPT2,
                    ParcelStatus::DELIVERY_ATTEMPT3,
                    ParcelStatus::DELIVERY_MAN_ASSIGN1,
                    ParcelStatus::RETURN_TO_COURIER,
                    ParcelStatus::RETURNING,
                    ParcelStatus::TRANSIT_SENDING_PROVINCE,
                    ParcelStatus::ON_THE_WAY_SENDING_PROVINCE,
                    ParcelStatus::ARRIVED_TO_SENDING_HUB,
                    ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                    ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1,
                    ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                    ParcelStatus::RETURN_RECEIVED_BY_MERCHANT],
            ];

            // Initialize an empty array for counts
            $parcelCounts = [];

            // Iterate through the status groups
            foreach ($statuses as $key => $statusGroup) {
                if($key == 'delivery_man_assign') {
                    $parcelCounts[$key] = Parcel::whereNotIn('status', $statusGroup)
                        ->where('delivery_man_id', $deliverymanId)
                        ->count();
                }else {
                    $parcelCounts[$key] = Parcel::whereIn('status', $statusGroup)
                        ->where('delivery_man_id', $deliverymanId)
                        ->count();
                }

            }

            $data['parcelCounts'] = $parcelCounts;

            $todayParcel = Parcel::with('parcelEvent')
                ->whereBetween('updated_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])
                ->where('delivery_man_id', $deliverymanId)
                ->orderBy('priority_type_id')
                ->orderByDesc('id')
                ->take(5) // Limit to 5 results
                ->get();

            $data['todayParcel'] =  ParcelResource::collection($todayParcel);


            return $this->responseWithSuccess(__('dashboard.delivery_man'),$data,200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('parcel.error_msg'), [],500);
        }
    }

    //profile
    public function profile(){
        try {

            $data                         = [];
            $data['user']                 = new  DeliverymanUserResource(auth()->user());
            $data['current_balance']      = auth()->user()->deliveryman->current_balance;

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
    public function parcelStatusUpdate(Request $request){
        switch ($request->status_action) {
            //return to qourier
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
                return $this->responseWithError(__('parcel.error_msg'), [],500);
            break;

        }
    }

}
