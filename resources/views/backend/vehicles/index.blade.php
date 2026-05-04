@extends('backend.partials.master')
@section('title')
{{ __('vehicle.title') }} {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="{{route('vehicles.index')}}" class="breadcrumb-link">{{ __('vehicle.title') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('vehicle.vehicle_list') }}</h4>
                    @if (hasPermission('vehicle_create') == true)
                    <div class="btn-group">
                        <a href="{{route('vehicles.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    </div>
                    @endif
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table   " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('vehicle.sl') }}</th>
                                    <th>{{ __('levels.hub') }}</th>
                                    <th>{{ __('levels.name') }}</th>
                                    <th>{{ __('levels.description') }}</th>
                                    <th>{{ __('vehicle.registration_number') }}</th>
                                    <th>{{ __('vehicle.capacity') }}</th>
                                    <th>{{ __('vehicle.size') }}</th>
                                    <th>{{ __('levels.status') }}</th>
                                    @if (hasPermission('vehicle_update') == true || hasPermission('vehicle_delete') == true)
                                    <th>{{ __('vehicle.action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($vehicles as $vehicle)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ @$vehicle->hub->name }}</td>
                                    <td>{{ $vehicle->name }}</td>
                                    <td>{{ $vehicle->description }}</td>
                                    <td>{{ $vehicle->registration_number }}</td>
                                    <td>{{ $vehicle->capacity }}</td>
                                    <td>{{ $vehicle->size }}</td>
                                    <td>{!! @$vehicle->my_status !!}</td>
                                    @if ( hasPermission('vehicle_update') == true || hasPermission('vehicle_delete') == true)
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if (hasPermission('vehicle_read') == true )
                                                <a href="{{route('vehicles.edit',$vehicle->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                @endif
                                                @if (hasPermission('vehicle_delete') == true )
                                                <form id="delete" value="Test" action="{{route('vehicles.delete',$vehicle->id)}}" method="POST" data-title="{{ __('delete.asset') }}">
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
                            </tbody>

                        </table>
                    </div>
                </div>

                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $vehicles->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $vehicles->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $vehicles->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $vehicles->total() }}</span>
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