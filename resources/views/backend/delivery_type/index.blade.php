@extends('backend.partials.master')
@section('title')
{{ __('levels.delivery_type') }} {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('menus.settings')}}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link">{{ __('levels.delivery_type') }}</a></li>
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
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.delivery_type') }}</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table   " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.title') }}</th>
                                    <th>{{ __('levels.status') }}</th>
                                    <th>{{ __('levels.position') }}</th>
                                    @if (hasPermission('delivery_type_update') || hasPermission('delivery_type_delete') )
                                    <th>{{ __('levels.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($delivery_types as $delivery_type)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$delivery_type->title}}</td>

                                    <td> {!! $delivery_type->my_status !!} </td>
                                    <td>{{$delivery_type->position}}</td>
                                    @if (hasPermission('delivery_type_update') || hasPermission('delivery_type_delete') )
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if (hasPermission('delivery_type_update'))
                                                <a href="{{route('delivery-type.edit',$delivery_type->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                @endif
                                                @if (hasPermission('delivery_type_delete') )
                                                <form id="delete" value="Test" action="{{route('delivery-type.delete',$delivery_type->id)}}" method="POST" data-title="{{ __('delete.delivery_type') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <input type="hidden" name="" value="Delivery Type" id="deleteTitle">
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
                </div>
                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $delivery_types->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $delivery_types->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $delivery_types->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $delivery_types->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>


            </div>
        </div>
        <!-- end data table  -->
    </div>
</div>
<!-- end wrapper  -->
@endsection()

<!-- css  -->
@push('styles')
<link rel="stylesheet" href="{{static_asset('backend')}}/css/switch.css">
@endpush
<!-- js  -->
@push('scripts')
<script src="{{ static_asset('backend/js/deliverytype/status.js') }}"></script>
@endpush