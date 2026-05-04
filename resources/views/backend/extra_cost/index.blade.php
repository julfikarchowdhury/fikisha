@extends('backend.partials.master')
@if(isset($editliquid))
@section('title')
{{ __('menus.extra_cost') }} {{ __('levels.edit') }}
@endsection
@else
@section('title')
{{ __('menus.extra_cost') }}
@endsection
@endif
@section('maincontent')
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{__('levels.dashboard')}}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('menus.settings')}}</a></li>
                            @if (isset($editliquid))
                            <li class="breadcrumb-item"><a href="{{ route('liquid-fragile.index') }}" class="breadcrumb-link">{{__('menus.extra_cost')}}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{__('levels.edit')}}</a></li>
                            @else
                            <li class="breadcrumb-item"><a href="{{ route('liquid-fragile.index') }}" class="breadcrumb-link active">{{__('menus.extra_cost')}}</a></li>
                            @endif
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->

    <div class="row">
        <!-- basic form -->
        <div class="col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    @if (isset($editliquid))
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.update') }} {{__('menus.extra_cost')}}</h4>
                    @else
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{__('menus.extra_cost')}}</h4>
                    @endif
                </div>
                <div class="card-body">
                    <!-- @if (isset($editliquid))
                        <h2 class="pageheader-title">{{ __('levels.update') }} {{__('menus.extra_cost')}}</h2>
                    @else
                        <h2 class="pageheader-title">{{__('menus.extra_cost')}}</h2>
                    @endif -->
                    <div class="table-responsive">
                        @if(isset($edit_id))
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{__('levels.title')}}</th>
                                    <th>{{__('levels.inside_charge')}}</th>
                                    <th>{{__('levels.outside_charge')}}</th>
                                    <th>{{__('levels.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($edit_id == 1)
                                <form action="{{ route('extra_cost.update', 1) }}" method="post">
                                    @method('PUT')
                                    @csrf
                                    <tr>
                                        <td>Rush 1 Hour Service</td>
                                        <td>
                                            <div class="form-group mb-0">
                                                <input type="number" name="charge" autocomplete="off" step="any" class="form-control" value="{{ SettingHelper('rush_hour_service_charge') }}" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group mb-0">
                                                <input type="number" name="outside_charge" step="any" autocomplete="off" class="form-control" value="{{ SettingHelper('rush_hour_service_outside_charge') }}" />
                                            </div>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                {{ __('levels.update') }}
                                            </button>
                                        </td>
                                    </tr>
                                </form>
                                @elseif ($edit_id == 2)
                                <form action="{{ route('extra_cost.update', 2) }}" method="post">
                                    @method('PUT')
                                    @csrf
                                    <tr>
                                        <td>Scheduled</td>
                                        <td>
                                            <div class="form-group mb-0">
                                                <input type="number" name="charge" step="any" autocomplete="off" class="form-control" value="{{ SettingHelper('scheduled_service_charge') }}" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group mb-0">
                                                <input type="number" name="outside_charge" step="any" autocomplete="off" class="form-control" value="{{ SettingHelper('scheduled_service_outside_charge') }}" />
                                            </div>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                {{ __('levels.update') }}
                                            </button>
                                        </td>
                                    </tr>
                                </form>
                                @endif
                            </tbody>
                        </table>
                        @else
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{__('levels.title')}}</th>
                                    @if(hasPermission('extra_cost_status_change') == true)
                                    <th>{{ __('levels.status') }}</th>
                                    @endif
                                    <th>{{ __('levels.inside_charge') }}</th>
                                    <th>{{ __('levels.outside_charge') }}</th>
                                    @if(hasPermission('extra_cost_update') == true)
                                    <th>{{ __('levels.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Rush 1 Hour Service</td>
                                    @if(hasPermission('extra_cost_status_change') == true)
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input switch-id" type="checkbox" data-url="{{ route('extra_cost.status') }}" data-id="1" id="switch-id" role="switch" value="rush_hour_service_status" @if(SettingHelper('rush_hour_service_status')==\App\Enums\Status::ACTIVE) checked @else @endif>
                                        </div>
                                    </td>
                                    @endif
                                    <td>{{settings()->currency}}{{ SettingHelper('rush_hour_service_charge')}}</td>
                                    <td>{{settings()->currency}}{{ SettingHelper('rush_hour_service_outside_charge')}}</td>
                                    @if(hasPermission('extra_cost_update') == true)
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{ route('extra_cost.edit', 1) }}" class="dropdown-item">
                                                    <i class="fas fa-edit" aria-hidden="true"></i> {{__('levels.edit')}}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Scheduled</td>
                                    @if(hasPermission('extra_cost_status_change') == true)
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input switch-id" type="checkbox" data-url="{{ route('extra_cost.status', 1) }}" data-id="2" id="switch-id" role="switch" value="scheduled_service_status" @if(SettingHelper('scheduled_service_status')==\App\Enums\Status::ACTIVE) checked @else @endif>
                                        </div>
                                    </td>
                                    @endif
                                    <td>{{settings()->currency}}{{ SettingHelper('scheduled_service_charge')}}</td>
                                    <td>{{settings()->currency}}{{ SettingHelper('scheduled_service_outside_charge')}}</td>
                                    @if(hasPermission('extra_cost_update') == true)
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{ route('extra_cost.edit', 2) }}" class="dropdown-item">
                                                    <i class="fas fa-edit" aria-hidden="true"></i> {{__('levels.edit')}}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- end basic form -->
    </div>
</div>
@endsection()

@push('styles')
<link rel="stylesheet" href="{{static_asset('backend')}}/css/switch.css">
@endpush
@push('scripts')
<script src="{{ static_asset('backend/js/extra_cost/status.js') }}"></script>
@endpush