@extends('backend.partials.master')
@section('title')
{{ __('AccountHeads.title') }} {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('menus.account')}}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{ __('AccountHeads.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('AccountHeads.title') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table   " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.type')}}</th>
                                    <th>{{ __('levels.name')}}</th>
                                    <th>{{ __('levels.status')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i=0;
                                @endphp
                                @foreach ($account_heads as $head)
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>{!! $head->my_type !!}</td>
                                    <td>{{ $head->name  }}</td>
                                    <td>{!! $head->my_status !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $account_heads->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $account_heads->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $account_heads->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $account_heads->total() }}</span>
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