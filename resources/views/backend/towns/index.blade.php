@extends('backend.partials.master')
@section('title')
{{ __('town.title') }} {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}"
                                    class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link">{{ __('town.title')
                                    }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.list')
                                    }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('town.title') }}</h4>
                    @if(hasPermission('town_create') == true )
                    <a href="{{route('towns.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('town.city') }}</th>
                                    <th>{{ __('town.district') }}</th>
                                    <th>{{ __('levels.name') }}</th>
                                    <th>{{ __('town.portal_code') }}</th>
                                    @if(hasPermission('town_update') == true || hasPermission('town_delete')
                                    == true)
                                    <th>{{ __('levels.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($towns as $town)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                   
                                    <td>
                                        @if(isset($town->city->name))
                                            {{ $town->city->name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($town->district->name))
                                            {{ $town->district->name }}
                                        @endif
                                    </td>
                                    <td>{{ $town->name }}</td>
                                    <td>{{ $town->portal_code }}</td>
                                    @if(hasPermission('town_update') == true || hasPermission('town_delete')
                                    == true)
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"> 
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if(hasPermission('town_update') == true )
                                                <a href="{{route('towns.edit',$town->id)}}" class="dropdown-item">
                                                    <i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}
                                                </a>
                                                @endif
                                                @if(hasPermission('town_delete') == true)
                                                <form id="delete" value="Test" data-title="{{ __('delete.delete_towns') }}" action="{{ route('towns.destroy',$town->id) }}" method="POST">
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
                    <span>{{ $towns->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $towns->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $towns->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $towns->total() }}</span>
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