@extends('backend.partials.master')
@section('title')
{{ __('asset.title') }} {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="{{route('asset.index')}}" class="breadcrumb-link">{{ __('asset.title') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('asset.asset_list') }}</h4>
                    @if (hasPermission('assets_create') == true)
                    <a href="{{route('asset.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    @endif
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table   " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('asset.sl') }}</th>
                                    <th>{{ __('asset.name') }}</th>
                                    <th>{{ __('asset.assetcategory_id') }}</th>
                                    <th>{{ __('asset.supplyer_name') }}</th>
                                    <th>{{ __('asset.quantity') }}</th>
                                    <th>{{ __('asset.warranty') }}</th>
                                    <th>{{ __('asset.invoice_no') }}</th>
                                    <th>{{ __('asset.amount') }}</th>
                                    @if (hasPermission('assets_update') == true || hasPermission('assets_delete') == true)
                                    <th>{{ __('asset.action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($assets as $asset)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{ $asset->name }}</td>
                                    <td>{{ $asset->assetcategorys->title }}</td>
                                    <td>{{ $asset->supplyer_name }}</td>
                                    <td>{{ $asset->quantity}}</td>
                                    <td>{{ $asset->warranty}}</td>
                                    <td>{{ $asset->invoice_no }}</td>
                                    <td>{{ $asset->amount }}</td>
                                    @if ( hasPermission('assets_update') == true || hasPermission('assets_delete') == true)
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if (hasPermission('assets_read') == true )
                                                <a href="{{route('asset.edit',$asset->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                @endif
                                                @if (hasPermission('assets_delete') == true )
                                                <form id="delete" value="Test" action="{{route('asset.delete',$asset->id)}}" method="POST" data-title="{{ __('delete.asset') }}">
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
                    <span>{{ $assets->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $assets->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $assets->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $assets->total() }}</span>
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