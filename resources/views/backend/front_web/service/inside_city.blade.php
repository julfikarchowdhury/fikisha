@extends('backend.front_web.service.index')
@section('service-table')
<div class="table-responsive">
    <table class="table   " style="width:100%">
        <thead>
            <tr>
                <th>{{ __('levels.id') }}</th>
                <th>{{ __('levels.title') }}</th>
                <th>{{ __('levels.image') }}</th>
                <th>{{ __('levels.description') }}</th>
                <th>{{ __('levels.position') }}</th>
                <th>{{ __('levels.status') }}</th>
                @if(hasPermission('service_update') || hasPermission('service_delete') )
                <th>{{ __('levels.actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php $i=1; @endphp
            @foreach($services as $service)
            <tr>
                <td>{{$i++}}</td>
                <td>{{@$service->shippingType->title}}</td>
                <td><img src="{{ @$service->image }}" /> </td>
                <td width="25%">{!! @$service->description !!}</td>
                <td>{{@$service->position}}</td>
                <td>{!!@$service->my_status!!}</td>
                @if(hasPermission('service_update') == true || hasPermission('service_delete') == true )
                <td>
                    <div class="row">
                        <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                            <i class="fa fa-cogs"></i>
                        </button>
                        <div class="dropdown-menu">
                            @if(hasPermission('service_update'))
                            <a href="{{route('service.edit',$service->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                            @endif
                            @if(hasPermission('service_delete') == true)
                            <form id="delete" value="Test" action="{{route('service.delete',$service->id)}}" method="POST" data-title="{{ __('Do you want to delete service ?') }}">
                                @method('DELETE')
                                @csrf
                                <input type="hidden" name="" value="service" id="deleteTitle">
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
<div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span>{{ @$services->links() }}</span>
    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
        {!! __('Showing') !!}
        <span class="font-medium">{{ @$services->firstItem() }}</span>
        {!! __('to') !!}
        <span class="font-medium">{{ @$services->lastItem() }}</span>
        {!! __('of') !!}
        <span class="font-medium">{{ @$services->total() }}</span>
        {!! __('results') !!}
    </p>
</div>
@endsection