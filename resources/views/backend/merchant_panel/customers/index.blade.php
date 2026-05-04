@extends('backend.partials.master')
@section('title')
    {{ __('sender_customer.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<!-- wrapper  -->
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('merchant-panel.customers.index') }}" class="breadcrumb-link">{{ __('sender_customer.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    <div class="row">
        <!-- table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('sender_customer.sender_customer_list') }}</h4>
                    <a href="{{route('merchant-panel.customers.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('sender_customer.sl') }}</th>
                                    <th>{{ __('levels.name') }}</th>
                                    <th>{{ __('levels.details') }}</th>
                                    <th>{{ __('levels.phone_number') }}</th>
                                    <th>{{ __('levels.email') }}</th>
                                    <th>{{ __('sender_customer.whatsapp_number') }}</th>
                                    <th>{{ __('vehicle.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>
                                        {{ $customer->name }}<br>
                                        <strong>{{ $customer->my_account_type }}</strong>
                                    </td>
                                    <td>
                                        {{ __('levels.address') }}: {{ $customer->address }}<br>
                                        {{ __('levels.province') }}: {{ @$customer->province->name }}({{ @$customer->province->id }})<br>
                                        {{ __('levels.city') }}: {{ @$customer->city->name }}<br>
                                    </td>
                                    <td>{{ $customer->phone_number }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->whatsapp_number }}</td>
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"> 
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{route('merchant-panel.customers.edit',$customer->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                <form id="delete" value="Test" action="{{route('merchant-panel.customers.delete',$customer->id)}}" method="POST" data-title="{{ __('delete.sender_customer') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="dropdown-item"><i class="fa fa-trash" aria-hidden="true"></i> {{ __('levels.delete') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $customers->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $customers->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $customers->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $customers->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
            </div>
        </div>
        <!-- end table  -->
    </div>
</div>
<!-- end wrapper  -->
@endsection()
