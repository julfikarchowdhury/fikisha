@extends('backend.partials.master')
@section('title')
{{ __('merchant.title') }} {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{ __('merchantmanage.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('customer.index') }}" class="breadcrumb-link">{{ __('merchant.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    <div class="row">
        <!-- data table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET">
                        <div class="row">
                            <div class="form-group col-12 col-xl-3 col-md-4">
                                <label for="province_id">{{ __('levels.province') }}</label>
                                <select id="province_id" name="province_id" class="form-control select2">
                                    <option value="">{{ __('levels.select') }} {{ __('levels.province') }}</option>
                                    @foreach (\App\Models\Backend\Province::all() as $province)
                                    <option value="{{ $province->id }}" @selected(request('province_id')==$province->id)>
                                        {{ $province->name }}({{ $province->province_code }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-xl-3 col-md-4">
                                <label for="city_id">{{ __('city.title') }}</label>
                                <select id="city_id" name="city_id" class="form-control select2">
                                    <option value="">{{ __('levels.select') }} {{ __('city.title') }}</option>
                                    @foreach (\App\Models\Backend\City::all() as $city)
                                    @if (request('province_id') == $city->province_id)
                                    <option value="{{ $city->id }}" @selected(request('city_id')==$city->id)>
                                        {{ $city->name }}
                                    </option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-xl-3 col-md-4">
                                <label for="name">{{ __('levels.name') }}</label>
                                <input type="text" id="name" name="name" placeholder="{{ __('user.title') }} {{ __('levels.name') }}" class="form-control" value="{{ request('name') }}">
                            </div>
                            <div class="form-group col-12 col-xl-3 col-md-4">
                                <label for="email">{{ __('levels.email') }}</label>
                                <input type="text" id="email" name="email" placeholder="{{ __('user.title') }} {{ __('levels.email') }}" class="form-control" value="{{ request('email') }}">
                            </div>
                            <div class="form-group col-12 col-xl-3 col-md-4">
                                <label for="phone">{{ __('levels.phone')}}</label>
                                <input type="text" id="phone" name="phone" placeholder="{{ __('levels.phone') }}" class="form-control" value="{{ request('phone') }}">
                            </div>
                            <div class="form-group col-12 col-xl-3 col-md-4">
                                <label for="merchant_unique_id">{{ __('levels.unique_id') }}</label>
                                <input type="text" id="merchant_unique_id" name="merchant_unique_id" placeholder="{{ __('levels.unique_id') }}" class="form-control" value="{{ request('merchant_unique_id') }}">
                            </div>
                            <div class="form-group col-12 col-md-3 pt-4">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-space btn-primary"><i class="fa fa-filter"></i> {{ __('levels.filter') }}</button>
                                    <a href="{{ route('customer.index') }}" class="btn btn-space btn-secondary"><i class="fa fa-eraser"></i> {{ __('levels.clear') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header flex-column d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap w-100">
                        <div class="d-flex parcelsearchFlex">
                            <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('merchant.title') }}</h4>
                        </div>
                        @if( hasPermission('customer_create') == true )
                        <a href="{{route('customer.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                        @endif
                    </div>
                    <div class="d-lg-none mt-2">
                        <input id="Psearch" class="form-control " type="text" placeholder="Search..">
                    </div>
                </div>
                <!-- <div class="row pl-4 pr-4 pt-4">
                    <div class="col-10">
                        <div class="d-flex parcelsearchFlex">
                            <p class="h3">{{ __('merchant.title') }}</p>
                        </div>
                    </div>
                    @if( hasPermission('customer_create') == true )
                    <div class="col-2">
                        <a href="{{route('customer.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    </div>
                    @endif
                    <div class="col-12 d-lg-none mt-2">
                        <input id="Psearch" class="form-control " type="text" placeholder="Search..">
                    </div>
                </div> -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table   " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.details') }}</th>
                                    <th>{{ __('levels.business_name') }}</th>
                                    <th>{{ __('levels.unique_id') }}</th>
                                    <th>{{ __('levels.phone') }}</th>
                                    <th>{{ __('levels.status') }}</th>
                                    <th>{{ __('levels.current_balance') }}</th>
                                    @if(hasPermission('customer_view') == true || hasPermission('customer_update') == true || hasPermission('customer_delete') == true)
                                    <th>{{ __('levels.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($merchants as $merchant)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>
                                        <div class="row">
                                            <div class="pr-3">
                                                <img src="{{$merchant->user->image}}" alt="user" class="rounded" width="40" height="40">
                                            </div>
                                            <div>
                                                <strong>{{ $merchant->user->name }}</strong>
                                                <p class="m-0 p-0">{{ $merchant->user->email }}</p>
                                                <p class="m-0 p-0">{{ __('levels.province') }}: {{ @$merchant->user->province->name }}({{ @$merchant->user->province->province_code }})</p>
                                                <p class="m-0 p-0">{{ __('levels.city') }}: {{ @$merchant->user->city->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ @$merchant->business_name ?: 'N/A' }}</td>
                                    <td>{{@$merchant->merchant_unique_id}}</td>
                                    <td>
                                        {{@$merchant->user->mobile}}
                                        @if($merchant->alternative_phone_number)
                                        ,{{ $merchant->alternative_phone_number }}
                                        @endif
                                    </td>
                                    <td>{!! $merchant->user->my_status !!}</td>
                                    <td>{{settings()->currency}}{{$merchant->current_balance}}</td>
                                    @if(
                                    hasPermission('customer_view') == true ||
                                    hasPermission('customer_update') == true ||
                                    hasPermission('customer_delete') == true
                                    )
                                    <td>
                                        <div class="row">

                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if( hasPermission('customer_view') == true )
                                                <a href="{{route('customer.view',$merchant->id)}}" class="dropdown-item"><i class="fa fa-eye" aria-hidden="true"></i> {{ __('levels.view') }}</a>
                                                @endif
                                                @if( hasPermission('customer_update') == true )
                                                <a href="{{route('customer.edit',$merchant->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                @endif
                                                @if( hasPermission('customer_delete') == true )
                                                <form id="delete" value="Test" action="{{route('customer.delete',$merchant->id)}}" method="POST" data-title="{{ __('delete.merchant') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <input type="hidden" name="" value="Merchant" id="deleteTitle">
                                                    <button type="submit" class="dropdown-item"><i class="fa fa-trash" aria-hidden="true"></i> {{ __('levels.delete') }}</button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span>{{ $merchants->links() }}</span>
                        <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                            {!! __('Showing') !!}
                            <span class="font-medium">{{ $merchants->firstItem() }}</span>
                            {!! __('to') !!}
                            <span class="font-medium">{{ $merchants->lastItem() }}</span>
                            {!! __('of') !!}
                            <span class="font-medium">{{ $merchants->total() }}</span>
                            {!! __('results') !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- end data table  -->
    </div>
</div>
<!-- end wrapper  -->
@endsection()
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
<!-- js  -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ static_asset('backend/js/parcel/parcel-search.js') }}"></script>
<script>
    $("#province_id").on('change', function() {
        var id = $(this).val();
        var op = "";
        $.ajax({
            type: "GET",
            url: "{{ url('admin/province/wise/city') }}/" + id,
            dataType: 'json',
            success: function(data) {
                op += '<option  value="">--Select City--</option>';
                for (var i = 0; i < data.length; i++) {
                    op += '<option  value="' + data[i].id + '">' + data[i].name + '</option>';
                }
                $('#city_id').html(op);
            }
        });
    });
</script>
@endpush