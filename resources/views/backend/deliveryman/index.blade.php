@extends('backend.partials.master')
@section('title')
Riders {{ __('levels.list') }}
@endsection
@section('maincontent')
<!-- wrapper  -->
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('deliveryman.index') }}" class="breadcrumb-link">Riders</a></li>
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
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('deliveryman.filter')}}" method="GET">
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
                                <input type="text" id="name" name="name" placeholder="{{ __('user.title') }} {{ __('levels.name') }}" class="form-control" value="{{old('name', $request->name)}}">
                                @error('name')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-xl-3 col-md-4">
                                <label for="email">{{ __('levels.email') }}</label>
                                <input type="text" id="email" name="email" placeholder="{{ __('user.title') }} {{ __('levels.email') }}" class="form-control" value="{{old('email', $request->email)}}">
                                @error('email')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-xl-3 col-md-4">
                                <label for="phone">{{ __('levels.phone')}}</label>
                                <input type="text" id="phone" name="phone" placeholder="{{ __('levels.phone') }}" class="form-control" value="{{old('phone', $request->phone)}}">
                                @error('phone')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-md-3 pt-4">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-space btn-primary"><i class="fa fa-filter"></i> {{ __('levels.filter') }}</button>
                                    <a href="{{ route('deliveryman.index') }}" class="btn btn-space btn-secondary"><i class="fa fa-eraser"></i> {{ __('levels.clear') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">Riders</h4>
                    @if( hasPermission('delivery_man_create') == true)
                    <a href="{{route('deliveryman.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    @endif
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table   " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.user') }}</th>
                                    <th>Vehicle Type</th>
                                    <th>{{ __('levels.phone') }}</th>
                                    <th>{{ __('levels.status') }}</th>
                                    <th>Rider Status</th>
                                    <th>KYC Submitted</th>
                                    <th>Approved At</th>
                                    @if( hasPermission('delivery_man_update') == true || hasPermission('delivery_man_delete') == true )
                                    <th>{{ __('levels.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(!blank($deliveryMans))
                                @php $i=1; @endphp
                                @foreach($deliveryMans as $deliveryman)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <img src="{{$deliveryman->user->image}}" alt="user" class="rounded" width="40" height="40">
                                            </div>
                                            <div class="col-md-9">
                                                <strong>{{$deliveryman->user->name}}</strong> <br />
                                                <strong>{{ $deliveryman->my_driver_type }}</strong>
                                                <p class="m-0 p-0">{{ $deliveryman->user->email }}</p>
                                                <p class="m-0 p-0">{{ __('levels.province') }}: {{ @$deliveryman->user->province->name }}({{ @$deliveryman->user->province->province_code }})</p>
                                                <p class="m-0 p-0">{{ __('levels.city') }}: {{ @$deliveryman->user->city->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $deliveryman->vehicle_type }}
                                    </td>
                                    <td>{{ $deliveryman->user->mobile }}</td>

                                    <td>
                                        {!! $deliveryman->user->my_status !!}
                                    </td>
                                    <td>
                                        {{ $deliveryman->rider_status_label ?? 'Approved' }}
                                    </td>
                                    <td>
                                        {{ $deliveryman->kyc_submitted_at ? \Illuminate\Support\Carbon::parse($deliveryman->kyc_submitted_at)->toDateTimeString() : '-' }}
                                    </td>
                                    <td>
                                        {{ $deliveryman->approved_at ? \Illuminate\Support\Carbon::parse($deliveryman->approved_at)->toDateTimeString() : '-' }}
                                    </td>
                                    @if( hasPermission('delivery_man_update') == true || hasPermission('delivery_man_delete') == true )
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">

                                                <a href="{{route('deliveryman.details',$deliveryman->id)}}" class="dropdown-item"><i class="fas fa-eye" aria-hidden="true"></i> {{ __('levels.details') }}</a>

                                                @if( hasPermission('delivery_man_update') == true )
                                                <a href="{{route('deliveryman.edit',$deliveryman->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                <a href="{{ route('deliveryman.account_status',$deliveryman->id) }}" class="dropdown-item"><i class="fas fa-user-check"></i> {{ __('levels.status') }}</a>
                                                <a href="{{ route('deliveryman.verification_status',$deliveryman->id) }}" class="dropdown-item"><i class="fas fa-check-circle"></i> {{ __('levels.verification_status') }}</a>
                                                <a href="{{ route('deliveryman.document_status',$deliveryman->id) }}" class="dropdown-item"><i class="fas fa-file-alt"></i> {{ __('levels.document_status') }}</a>
                                                <div class="dropdown-divider"></div>
                                                <a href="{{ route('deliveryman.kyc.index') }}" class="dropdown-item"><i class="fas fa-id-card"></i> KYC Review</a>
                                                @endif
                                                @if(hasPermission('delivery_man_delete') == true )
                                                <form action="{{route('deliveryman.delete',$deliveryman->id)}}" method="POST" id="delete" data-title="{{ __('delete.delivery_man') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="dropdown-item"><i class="fa fa-trash" aria-hidden="true"></i> {{ __('levels.delete') }}</button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $deliveryMans->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $deliveryMans->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $deliveryMans->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $deliveryMans->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
            </div>
        </div>
        <!-- end data table  -->
    </div>
</div>
<!-- end wrapper  -->
@endsection
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