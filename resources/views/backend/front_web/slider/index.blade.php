@extends('backend.partials.master')
@section('title')
{{ __('levels.slider') }} {{ __('levels.list') }}
@endsection
@section('maincontent')

<div class="container-fluid  dashboard-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('levels.front_web')}}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link">{{ __('levels.slider') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.slider') }}</h4>
                    @if(hasPermission('slider_create'))
                    <a href="{{route('slider.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table   " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.slider') }}</th>
                                    <th>{{ __('levels.title') }}</th>
                                    <th>{{ __('levels.small_title') }}</th>
                                    <th>{{ __('levels.position') }}</th>
                                    <th>{{ __('levels.status') }}</th>
                                    @if(hasPermission('slider_update') || hasPermission('slider_delete') )
                                    <th>{{ __('levels.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($sliders as $slider)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>
                                        <img class="object-fit-contain" height="50" width="50"
                                            src="{{ $slider->slider_image }}" alt="slider">
                                    </td>
                                    <td> {{ @$slider->title}} </td>
                                    <td> {{ @$slider->small_title }} </td>
                                    <td> {{ $slider->position }} </td>
                                    <td> {!! @$slider->my_status!!} </td>
                                    @if(hasPermission('slider_update') == true || hasPermission('slider_delete') == true )
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if(hasPermission('slider_update'))
                                                <a href="{{route('slider.edit',$slider->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                @endif
                                                @if(hasPermission('slider_delete') == true)
                                                <form id="delete" value="Test" action="{{route('slider.delete',$slider->id)}}" method="POST" data-title="{{ __('Do you want to delete slider link ?') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <input type="hidden" name="" value="Slider" id="deleteTitle">
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
                    <span>{{ @$sliders->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ @$sliders->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ @$sliders->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ @$sliders->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection()