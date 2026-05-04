<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SenderCustomer\StoreSenderCustomerRequest;
use App\Http\Requests\SenderCustomer\UpdateSenderCustomerRequest;
use App\Repositories\SenderCustomer\SenderCustomerInterface;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class SenderCustomerController extends Controller
{
    protected $repo;

    public function __construct(SenderCustomerInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($sender_id)
    {
        $sender_customers = $this->repo->senderCustomer($sender_id);
        return view('backend.sender_customers.index', compact('sender_customers', 'sender_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($sender_id)
    {
        return view('backend.sender_customers.create', compact('sender_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSenderCustomerRequest $request)
    {
        if ($this->repo->store($request)) {
            Toastr::success('Sender Customer successfully added.', __('message.success'));
            return redirect()->route('sender_customers.index', $request->sender_id);
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
        $sender_customer    = $this->repo->get($id);
        return view('backend.sender_customers.edit', compact('sender_customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSenderCustomerRequest $request, $id)
    {
        if ($this->repo->update($request, $id)) {
            Toastr::success('Sender Customer successfully Update.', __('message.success'));
            return redirect()->route('sender_customers.index', $request->sender_id);
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
            Toastr::success('Sender Customer successfully deleted.', __('message.success'));
            return redirect()->back();
        } else {
            Toastr::error('Something went wrong.', __('message.success'));
            return redirect()->back();
        }
    }
}
