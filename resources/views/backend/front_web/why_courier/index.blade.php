@extends('backend.partials.master')
@section('title')
{{ __('levels.why_courier') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid  dashboard-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('levels.front_web')}}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link">{{ __('levels.why_courier') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.why_courier') }}</h4>
                    @if(hasPermission('why_courier_create'))
                    <a href="{{route('why.courier.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table   " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.icon') }}</th>
                                    <th>{{ __('levels.content') }}</th>
                                    <th>{{ __('levels.position') }}</th>
                                    <th>{{ __('levels.status') }}</th>
                                    @if(hasPermission('why_courier_update') || hasPermission('why_courier_delete') )
                                    <th>{{ __('levels.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($whycouriers as $whycourier)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td style="vertical-align: middle;font-size:20px">

                                        <i class="{{ $whycourier->icon }} text-dark "></i> {{ $whycourier->icon }}

                                    </td>
                                    <td width="50%">{{@$whycourier->content}}</td>
                                    <td>{{@$whycourier->position}}</td>
                                    <td>{!!@$whycourier->my_status!!}</td>
                                    @if(hasPermission('why_courier_update') == true || hasPermission('why_courier_delete') == true )
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if(hasPermission('why_courier_update'))
                                                <a href="{{route('why.courier.edit',$whycourier->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                @endif
                                                @if(hasPermission('why_courier_delete') == true)
                                                <form id="delete" value="Test" action="{{route('why.courier.delete',$whycourier->id)}}" method="POST" data-title="{{ __('Do you want to delete why courier ?') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <input type="hidden" name="" value="why courier" id="deleteTitle">
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
                    <span>{{ @$whycouriers->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ @$whycouriers->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ @$whycouriers->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ @$whycouriers->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection