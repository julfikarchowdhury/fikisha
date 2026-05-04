<?php

namespace App\Http\Controllers\Backend\MerchantPanel;

use App\Http\Controllers\Controller;
use App\Http\Requests\SenderCustomer\StoreSenderCustomerRequest;
use App\Http\Requests\SenderCustomer\UpdateSenderCustomerRequest;
use App\Repositories\SenderCustomer\SenderCustomerInterface;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class SenderPanelCustomerController extends Controller
{
    protected $repo;

    public function __construct(SenderCustomerInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = $this->repo->senderCustomer(auth()->user()->merchant->id);
        return view('backend.merchant_panel.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.merchant_panel.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSenderCustomerRequest $request)
    {
        if ($this->repo->store($request)) {
            Toastr::success('Customer successfully added.', __('message.success'));
            return redirect()->route('merchant-panel.customers.index');
        } else {
            Toastr::error('Something went wrong.', __('message.error'));
            return redirect()->back()->withInput($request->all());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $customer    = $this->repo->get($id);
        return view('backend.merchant_panel.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSenderCustomerRequest $request, $id)
    {
        if ($this->repo->update($request, $id)) {
            Toastr::success('Customer successfully Update.', __('message.success'));
            return redirect()->route('merchant-panel.customers.index');
        } else {
            Toastr::error('Something went wrong.', __('message.success'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if ($this->repo->delete($id)) {
            Toastr::success('Customer successfully deleted.', __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error('Something went wrong.', __('message.success'));
            return redirect()->back();
        }
    }
}
