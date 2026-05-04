@extends('backend.partials.master')
@if(isset($editliquid))
@section('title')
{{ __('menus.liquid_fragile') }} {{ __('levels.edit') }}
@endsection
@else
@section('title')
{{ __('menus.liquid_fragile') }}
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
                            <li class="breadcrumb-item"><a href="{{ route('liquid-fragile.index') }}" class="breadcrumb-link">{{__('menus.liquid_fragile')}}</a></li>

                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{__('levels.edit')}}</a></li>
                            @else

                            <li class="breadcrumb-item"><a href="{{ route('liquid-fragile.index') }}" class="breadcrumb-link active">{{__('menus.liquid_fragile')}}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.update') }} {{__('menus.liquid_fragile')}}</h4>
                    @else
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{__('menus.liquid_fragile')}}</h4>
                    @endif
                </div>
                <div class="card-body">
                    <!-- @if (isset($editliquid))
                        <h2 class="pageheader-title">{{ __('levels.update') }} {{__('menus.liquid_fragile')}}</h2>
                    @else
                        <h2 class="pageheader-title">{{__('menus.liquid_fragile')}}</h2>
                    @endif -->
                    <div class="table-responsive">
                        @if(isset($editliquid))
                        <form action="{{ route('liquid.fragile.update') }}" method="post">
                            @method('PUT')
                            @csrf
                            <table class="table   " style="width:100%">
                                <thead>
                                    <tr>
                                        <th>{{__('liquid.title')}}</th>
                                        <th>{{__('liquid.inside_charge')}}</th>
                                        <th>{{__('liquid.outside_charge')}}</th>
                                        <th>{{__('levels.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1; @endphp
                                    <tr>
                                        <td>{{ __('placeholder.Liquid_Fragile') }}</td>
                                        <td>
                                            <div class="form-group mb-0">
                                                <input type="number" name="charge" step="any" autocomplete="off" class="form-control" value="{{ SettingHelper('fragile_liquid_charge') }}" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group mb-0">
                                                <input type="number" name="outside_charge" step="any" autocomplete="off" class="form-control" value="{{ SettingHelper('fragile_liquid_outside_charge') }}" />
                                            </div>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                {{ __('levels.update') }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                        @else

                        @method('PUT')
                        @csrf
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{__('liquid.title')}}</th>
                                    @if(hasPermission('liquid_status_change') == true)
                                    <th>{{__('levels.status')}}</th>
                                    @endif
                                    <th>{{__('liquid.inside_charge')}}</th>
                                    <th>{{__('liquid.outside_charge')}}</th>
                                    @if(hasPermission('liquid_fragile_update') == true)
                                    <th>{{__('levels.actions')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                <tr>
                                    <td>Liquid/Fragile</td>
                                    @if(hasPermission('liquid_status_change') == true)
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" data-url="{{ route('liquid-fragile.status') }}" id="switch-id" role="switch" value="fragile_liquid_status" @if(SettingHelper('fragile_liquid_status')==\App\Enums\Status::ACTIVE) checked @else @endif>
                                        </div>
                                    </td>
                                    @endif
                                    <td>{{settings()->currency}}{{ SettingHelper('fragile_liquid_charge')}}</td>
                                    <td>{{settings()->currency}}{{ SettingHelper('fragile_liquid_outside_charge')}}</td>
                                    @if(hasPermission('liquid_fragile_update') == true)
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{route('liquid.fragile.edit')}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{__('levels.edit')}}</a>
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
<script src="{{ static_asset('backend/js/liquid/status.js') }}"></script>
@endpush