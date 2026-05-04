<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Enums\RiderStatus;
use App\Enums\Status;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\RiderWallet;
use App\Repositories\DeliveryMan\DeliveryManInterface;
use Illuminate\Http\Request;
use App\Http\Requests\DeliveryMan\DeliveryManRequest;
use Brian2694\Toastr\Facades\Toastr;

class DeliveryManController extends Controller
{
    protected $repo;
    public function __construct(DeliveryManInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $deliveryMans = $this->repo->all();
        return view('backend.deliveryman.index', compact('deliveryMans', 'request'));
    }

    public function filter(Request $request)
    {
        $deliveryMans = $this->repo->filter($request);
        return view('backend.deliveryman.index', compact('deliveryMans', 'request'));
    }

    public function create()
    {
        $data['countries'] = $this->repo->all_country();
        $data['cities'] = $this->repo->all_city();
        $data['districts'] = $this->repo->all_district();
        $data['towns'] = $this->repo->all_town();
        return view('backend.deliveryman.create', $data);
    }

    public function store(DeliveryManRequest $request)
    {
        if ($this->repo->store($request)) {
            Toastr::success(__('deliveryman.added_msg'), __('message.success'));
            return redirect()->route('deliveryman.index');
        } else {
            Toastr::error(__('deliveryman.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['countries'] = $this->repo->all_country();
        $data['cities'] = $this->repo->all_city();
        $data['districts'] = $this->repo->all_district();
        $data['towns'] = $this->repo->all_town();
        $data['deliveryman'] = $this->repo->get($id);
        return view('backend.deliveryman.edit', $data);
    }

    public function update(DeliveryManRequest $request)
    {
        if ($this->repo->update($request->id, $request)) {
            Toastr::success(__('deliveryman.update_msg'), __('message.success'));
            return redirect()->route('deliveryman.index');
        } else {
            Toastr::error(__('deliveryman.error_msg'), __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    public function destroy($id)
    {
        $this->repo->delete($id);
        Toastr::success(__('deliveryman.delete_msg'), __('message.success'));
        return back();
    }

    public function details($id)
    {

        $deliveryman = $this->repo->get($id);
        return view('backend.deliveryman.details', compact('deliveryman'));
    }

    public function accountStatus($id)
    {
        if ($this->repo->accountStatus($id)) {
            Toastr::success(__('deliveryman.update_msg'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('deliveryman.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    public function verificationStatus($id)
    {
        if ($this->repo->verificationStatus($id)) {
            Toastr::success(__('deliveryman.update_msg'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('deliveryman.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    public function documentStatus($id)
    {
        if ($this->repo->documentStatus($id)) {
            Toastr::success(__('deliveryman.update_msg'), __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error(__('deliveryman.error_msg'), __('message.error'));
            return redirect()->back();
        }
    }

    public function kycIndex(Request $request)
    {
        $status = $request->query('status');
        $query = DeliveryMan::with('user');

        if ($status !== null && $status !== '') {
            $query->where('rider_status', (int) $status);
        } else {
            $query->where(function ($builder) {
                $builder->whereIn('rider_status', [RiderStatus::PENDING_KYC, RiderStatus::UNDER_REVIEW])
                    ->orWhereNull('rider_status');
            });
        }

        $deliveryMans = $query->orderByDesc('id')->paginate(10);

        return view('backend.deliveryman.kyc_review', compact('deliveryMans', 'status'));
    }

    public function approveKyc($id)
    {
        $deliveryman = $this->repo->get($id);
        if (!$deliveryman || !$deliveryman->user) {
            Toastr::error(__('deliveryman.error_msg'), __('message.error'));
            return redirect()->back();
        }

        $deliveryman->rider_status = RiderStatus::APPROVED;
        $deliveryman->approved_at = now();
        $deliveryman->rejected_at = null;
        $deliveryman->rejection_reason = null;
        $deliveryman->save();

        $deliveryman->user->status = Status::ACTIVE;
        $deliveryman->user->document_status = Status::ACTIVE;
        $deliveryman->user->submit_status = Status::ACTIVE;
        $deliveryman->user->save();

        RiderWallet::firstOrCreate(
            ['rider_id' => $deliveryman->user_id],
            ['balance' => 0, 'pending_withdraw_amount' => 0]
        );

        Toastr::success('Rider approved successfully.', __('message.success'));
        return redirect()->back();
    }

    public function rejectKyc(Request $request, $id)
    {
        $deliveryman = $this->repo->get($id);
        if (!$deliveryman || !$deliveryman->user) {
            Toastr::error(__('deliveryman.error_msg'), __('message.error'));
            return redirect()->back();
        }

        $deliveryman->rider_status = RiderStatus::REJECTED;
        $deliveryman->rejected_at = now();
        $deliveryman->rejection_reason = $request->input('note');
        $deliveryman->save();

        Toastr::success('Rider rejected successfully.', __('message.success'));
        return redirect()->back();
    }

    public function requestReupload(Request $request, $id)
    {
        $deliveryman = $this->repo->get($id);
        if (!$deliveryman || !$deliveryman->user) {
            Toastr::error(__('deliveryman.error_msg'), __('message.error'));
            return redirect()->back();
        }

        $deliveryman->rider_status = RiderStatus::REJECTED;
        $deliveryman->approved_at = null;
        $deliveryman->rejected_at = now();
        $deliveryman->rejection_reason = $request->input('note');
        $deliveryman->save();

        Toastr::success('Re-upload requested successfully.', __('message.success'));
        return redirect()->back();
    }
}
