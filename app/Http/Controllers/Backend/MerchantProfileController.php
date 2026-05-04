<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MerchantProfile\UpdateRequest;
use App\Http\Requests\MerchantProfile\UpdatePasswordRequest;
use App\Repositories\MerchantProfile\MerchantProfileInterface;
use Brian2694\Toastr\Facades\Toastr;

class MerchantProfileController extends Controller
{
    protected $repo;
    public function __construct(MerchantProfileInterface $repo)
    {
        $this->repo = $repo;
    }

    public function view()
    {
        $merchat = $this->repo->get(auth()->id());
        return view('backend.merchant_profile.index', compact('merchat'));
    }

    public function create()
    {
        $merchat = $this->repo->get(auth()->id());
        return view('backend.merchant_profile.update', compact('merchat'));
    }

    public function changePassword()
    {
        $merchat = $this->repo->get(auth()->id());
        return view('backend.merchant_profile.change_password', compact('merchat'));
    }

    public function update(UpdateRequest $request)
    {
        if ($this->repo->update(auth()->id(), $request)) {
            Toastr::success('Merchant Profile updated successfully.', __('message.success'));
            return redirect()->route('merchant-profile.index');
        } else {
            Toastr::error('Something went wrong.', __('message.error'));
            return redirect()->back();
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $result = $this->repo->updatePassword(auth()->id(), $request);
        if ($result == 1) {
            Toastr::success('Password updated successfully', __('message.success'));
            return redirect()->route('merchant-profile.index');
        } elseif ($result == 0) {
            Toastr::warning('Old password not match!', __('message.warning'));
            return redirect()->back()->withInput();
        } else {
            Toastr::error('Something went wrong.', __('message.error'));
            return redirect()->back();
        }
    }
}
