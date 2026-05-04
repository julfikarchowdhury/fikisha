@extends('backend.partials.master')
@section('title')
    {{ __('parcel.title') }} {{ __('levels.logs') }}
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
                            <li class="breadcrumb-item"><a href="{{route('parcel.index')}}" class="breadcrumb-link">{{ __('parcel.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{__('levels.logs')}}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <section class="mt-5 pt-5">
        <div class="row">
            <div class="col-xl-12 ">
                <div class="position-relative ">
                    <section class="cd-timeline js-cd-timeline">
                        <div class="cd-timeline__container">
                            @foreach ($parcelevents as $key=>$log)
                                @php
                                    if(!empty($log->cancel_parcel_id)): 
                                        $cancel   = ' cancel';
                                        $danger   = 'danger'; 
                                    else:
                                        $cancel   = null;
                                        $danger   = null; 
                                    endif; 
                                @endphp
                                @switch($log->parcel_status)
                                    @case(\App\Enums\ParcelStatus::PICKUP_ASSIGN)
                                        <div class="cd-timeline__block js-cd-block"> 
                                            <div class="cd-timeline__img cd-timeline__img--picture js-cd-img {{ isset($danger)? 'bg-danger':'' }}">
                                                <i class="timeline_icon fas {{ isset($danger)? 'fa-close':'fa-check' }}" aria-hidden="true"></i>
                                            </div>
                                            <!-- cd-timeline__img -->
                                            <div class="cd-timeline__content js-cd-content">
                                                <strong>{{__('parcelLogs.'.$log->parcel_status)}} {{ isset($danger)? $cancel:'' }}</strong><br>
                                                <span>{{__('parcel.pickup_man')}}: {{isset($log->pickupman)? $log->pickupman->user->name:''}}</span><br>
                                                <span>{{__('levels.phone_number')}}: {{isset($log->pickupman)? $log->pickupman->user->mobile:''}}</span><br>
                                                <span>{{__('levels.note')}}: {{$log->note}}</span><br/>

                                                <strong>{{ __('levels.created_by') }}</strong><br/>
                                                <span>{{ __('levels.name') }}: {{ $log->user->name }} </span><br/>
                                                <span>{{ __('levels.phone_number') }}: {{ $log->user->mobile }} </span><br/>

                                                <div class="cd-timeline__date">
                                                    <strong>{!! dateFormat2($log->created_at) !!}</strong><br>
                                                    <small>{!! date('h:i a', strtotime($log->created_at)) !!}</small>
                                                </div>
                                            </div>
                                            <!-- cd-timeline__content -->
                                        </div>
                                    @break
                                    @case(\App\Enums\ParcelStatus::PICKUP_RE_SCHEDULE)
                                        <div class="cd-timeline__block js-cd-block">
                                            <div class="cd-timeline__img cd-timeline__yellow js-cd-img {{ isset($danger)? 'bg-danger':'' }}">
                                                <i class="timeline_icon fas {{ isset($danger)? 'fa-close':'fa-hourglass-end' }}" aria-hidden="true"></i>
                                            </div>
                                            <!-- cd-timeline__img -->
                                            <div class="cd-timeline__content js-cd-content">
                                                <strong>{{__('parcelLogs.'.$log->parcel_status)}} {{ isset($danger)? $cancel:'' }}</strong><br>
                                                <span>{{__('parcel.pickup_man')}}: {{isset($log->pickupman)? $log->pickupman->user->name:''}}</span><br>
                                                <span>{{__('levels.phone_number')}}: {{isset($log->pickupman)? $log->pickupman->user->mobile:''}}</span><br>
                                                <span>{{__('levels.note')}}: {{$log->note}}</span><br/>

                                                <strong>{{ __('levels.created_by') }}</strong><br/>
                                                <span>{{ __('levels.name') }}: {{ $log->user->name }} </span><br/>
                                                <span>{{ __('levels.phone_number') }}: {{ $log->user->mobile }} </span><br/>

                                                <div class="cd-timeline__date">
                                                    <strong>{!! dateFormat2($log->created_at) !!}</strong><br>
                                                    <small>{!! date('h:i a', strtotime($log->created_at)) !!}</small>
                                                </div>
                                            </div>
                                            <!-- cd-timeline__content -->
                                        </div>
                                    @break
                                    @case(\App\Enums\ParcelStatus::RECEIVED_BY_PICKUP_MAN)
                                        <div class="cd-timeline__block js-cd-block">
                                            <div class="cd-timeline__img cd-timeline__img--picture js-cd-img {{ isset($danger)? 'bg-danger':'' }}">
                                                <i class="timeline_icon fas {{ isset($danger)? 'fa-close':'fa-check' }}" aria-hidden="true"></i>
                                            </div>
                                            <!-- cd-timeline__img -->
                                            <div class="cd-timeline__content js-cd-content">
                                                <strong>{{__('parcelLogs.'.$log->parcel_status)}} {{ isset($danger)? $cancel:'' }}</strong><br>
                                                <span>{{__('levels.note')}}: {{$log->note}}</span><br/>
                                                <strong>{{ __('levels.created_by') }}</strong><br/>
                                                <span>{{ __('levels.name') }}: {{ $log->user->name }} </span><br/>
                                                <span>{{ __('levels.phone_number') }}: {{ $log->user->mobile }} </span><br/>

                                                <div class="cd-timeline__date">
                                                    <strong>{!! dateFormat2($log->created_at) !!}</strong><br>
                                                    <small>{!! date('h:i a', strtotime($log->created_at)) !!}</small>
                                                </div>
                                            </div>
                                            <!-- cd-timeline__content -->
                                        </div>
                                    @break
                                    @case(\App\Enums\ParcelStatus::RECEIVED_WAREHOUSE)
                                        <div class="cd-timeline__block js-cd-block">
                                            <div class="cd-timeline__img cd-timeline__img--picture js-cd-img {{ isset($danger)? 'bg-danger':'' }}">
                                                <i class="timeline_icon fas {{ isset($danger)? 'fa-close':'fa-check' }}" aria-hidden="true"></i>
                                            </div>
                                            <!-- cd-timeline__img -->
                                            <div class="cd-timeline__content js-cd-content">
                                                <strong>{{__('parcelLogs.'.$log->parcel_status)}} {{ isset($danger)? $cancel:'' }}</strong><br>
                                                <span>{{__('parcelLogs.hub_name')}}: {{$log->hub->name}}</span><br>
                                                <span>{{__('levels.phone_number')}}: {{$log->hub->phone}}</span><br/>
                                                <span>{{__('levels.note')}}: {{$log->note}}</span><br/>

                                                <strong>{{ __('levels.created_by') }}</strong><br/>
                                                <span>{{ __('levels.name') }}: {{ $log->user->name }} </span><br/>
                                                <span>{{ __('levels.phone_number') }}: {{ $log->user->mobile }} </span><br/>

                                                <div class="cd-timeline__date">
                                                    <strong>{!! dateFormat2($log->created_at) !!}</strong><br>
                                                    <small>{!! date('h:i a', strtotime($log->created_at)) !!}</small>
                                                </div>
                                            </div>
                                            <!-- cd-timeline__content -->
                                        </div>
                                    @break
                                    @case(\App\Enums\ParcelStatus::TRANSFER_TO_HUB)
                                        <div class="cd-timeline__block js-cd-block">
                                            <div class="cd-timeline__img cd-timeline__img--picture js-cd-img {{ isset($danger)? 'bg-danger':'' }}">
                                                <i class="timeline_icon fas {{ isset($danger)? 'fa-close':'fa-check' }}" aria-hidden="true"></i>
                                            </div>
                                            <!-- cd-timeline__img -->
                                            <div class="cd-timeline__content js-cd-content">
                                                <strong>{{__('parcelLogs.'.$log->parcel_status)}} {{ isset($danger)? $cancel:'' }}</strong><br>
                                                <span>{{__('parcelLogs.hub_name')}}: {{$log->hub->name}}</span><br>
                                                <span>{{__('levels.phone_number')}}: {{$log->hub->phone}}</span><br/>
                                                <span>{{__('parcelLogs.delivery_man')}}: {{ isset($log->transferDeliveryman) ? $log->transferDeliveryman->user->name:''}}</span><br/>
                                                <span>{{__('levels.phone_number')}}: {{ isset($log->transferDeliveryman) ? $log->transferDeliveryman->user->mobile:''}}</span><br/>
                                                <span>{{__('levels.note')}}: {{$log->note}}</span><br/>

                                                <strong>{{ __('levels.created_by') }}</strong><br/>
                                                <span>{{ __('levels.name') }}: {{ $log->user->name }} </span><br/>
                                                <span>{{ __('levels.phone_number') }}: {{ $log->user->mobile }} </span><br/>

                                                <div class="cd-timeline__date">
                                                    <strong>{!! dateFormat2($log->created_at) !!}</strong><br>
                                                    <small>{!! date('h:i a', strtotime($log->created_at)) !!}</small>
                                                </div>
                                            </div>
                                            <!-- cd-timeline__content -->
                                        </div>
                                    @break
                               
                                    @case(\App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN)
                                        <div class="cd-timeline__block js-cd-block">
                                            <div class="cd-timeline__img cd-timeline__img--picture js-cd-img {{ isset($danger)? 'bg-danger':'' }}">
                                                <i class="timeline_icon fas {{ isset($danger)? 'fa-close':'fa-check' }}" aria-hidden="true"></i>
                                            </div>
                                            <!-- cd-timeline__img -->
                                            <div class="cd-timeline__content js-cd-content">
                                                <strong>{{__('parcelLogs.'.$log->parcel_status)}} {{ isset($danger)? $cancel:'' }}</strong><br>
                                                <span>{{__('parcelLogs.delivery_man')}}: {{isset($log->deliveryMan)? $log->deliveryMan->user->name:''}}</span><br>
                                                <span>{{__('levels.phone_number')}}: {{isset($log->deliveryMan)? $log->deliveryMan->user->mobile:''}}</span><br/>
                                                <span>{{__('levels.note')}}: {{$log->note}}</span><br/>

                                                <strong>{{ __('levels.created_by') }}</strong><br/>
                                                <span>{{ __('levels.name') }}: {{ $log->user->name }} </span><br/>
                                                <span>{{ __('levels.phone_number') }}: {{ $log->user->mobile }} </span><br/>

                                                <div class="cd-timeline__date">
                                                    <strong>{!! dateFormat2($log->created_at) !!}</strong><br>
                                                    <small>{!! date('h:i a', strtotime($log->created_at)) !!}</small>
                                                </div>
                                            </div>
                                            <!-- cd-timeline__content -->
                                        </div>
                                    @break
                
                                    @case(\App\Enums\ParcelStatus::DELIVERY_RE_SCHEDULE)
                                        <div class="cd-timeline__block js-cd-block">
                                            <div class="cd-timeline__img cd-timeline__yellow js-cd-img {{ isset($danger)? 'bg-danger':'' }}">
                                                <i class="timeline_icon fas {{ isset($danger)? 'fa-close':'fa-hourglass-end' }}" aria-hidden="true"></i>
                                            </div>
                                            <!-- cd-timeline__img -->
                                            <div class="cd-timeline__content js-cd-content">
                                                <strong>{{__('parcelLogs.'.$log->parcel_status)}} {{ isset($danger)? $cancel:'' }}</strong><br>
                                                <span>{{__('parcelLogs.delivery_man')}}: {{isset($log->deliveryMan)? $log->deliveryMan->user->name:''}}</span><br>
                                                <span>{{__('levels.phone_number')}}: {{isset($log->deliveryMan)? $log->deliveryMan->user->mobile:''}}</span><br/>
                                                <span>{{__('levels.note')}}: {{$log->note}}</span><br/>

                                                <strong>{{ __('levels.created_by') }}</strong><br/>
                                                <span>{{ __('levels.name') }}: {{ $log->user->name }} </span><br/>
                                                <span>{{ __('levels.phone_number') }}: {{ $log->user->mobile }} </span><br/>

                                                <div class="cd-timeline__date">
                                                    <strong>{!! dateFormat2($log->created_at) !!}</strong><br>
                                                    <small>{!! date('h:i a', strtotime($log->created_at)) !!}</small>
                                                </div>
                                            </div>
                                            <!-- cd-timeline__content -->
                                        </div>
                                    @break
                                    @case(\App\Enums\ParcelStatus::DELIVERED)
                                        <div class="cd-timeline__block js-cd-block">
                                            <div class="cd-timeline__img cd-timeline__img--picture js-cd-img {{ isset($danger)? 'bg-danger':'' }}">
                                                <i class="timeline_icon fas {{ isset($danger)? 'fa-close':'fa-check' }}" aria-hidden="true"></i>
                                            </div>
                                            <!-- cd-timeline__img -->
                                            <div class="cd-timeline__content js-cd-content">
                                                <strong>{{__('parcelLogs.'.$log->parcel_status)}} {{ isset($danger)? $cancel:'' }}</strong><br>
                                                <span>{{__('levels.note')}}: {{$log->note}}</span><br/>

                                                <strong>{{ __('levels.created_by') }}</strong><br/>
                                                <span>{{ __('levels.name') }}: {{ $log->user->name }} </span><br/>
                                                <span>{{ __('levels.phone_number') }}: {{ $log->user->mobile }} </span><br/>

                                                <div class="cd-timeline__date">
                                                    <strong>{!! dateFormat2($log->created_at) !!}</strong><br>
                                                    <small>{!! date('h:i a', strtotime($log->created_at)) !!}</small>
                                                </div>
                                            </div>
                                            <!-- cd-timeline__content -->
                                        </div>
                                    @break
                                    @case(\App\Enums\ParcelStatus::PARTIAL_DELIVERED)
                                        <div class="cd-timeline__block js-cd-block">
                                            <div class="cd-timeline__img cd-timeline__img--picture js-cd-img {{ isset($danger)? 'bg-danger':'' }}">
                                                <i class="timeline_icon fas {{ isset($danger)? 'fa-close':'fa-check' }}" aria-hidden="true"></i>
                                            </div>
                                            <!-- cd-timeline__img -->
                                            <div class="cd-timeline__content js-cd-content">
                                                <strong>{{__('parcelLogs.'.$log->parcel_status)}} {{ isset($danger)? $cancel:'' }}</strong><br>
                                                <span>{{__('levels.note')}}: {{$log->note}}</span><br/>

                                                <strong>{{ __('levels.created_by') }}</strong><br/>
                                                <span>{{ __('levels.name') }}: {{ $log->user->name }} </span><br/>
                                                <span>{{ __('levels.phone_number') }}: {{ $log->user->mobile }} </span><br/>

                                                <div class="cd-timeline__date">
                                                    <strong>{!! dateFormat2($log->created_at) !!}</strong><br>
                                                    <small>{!! date('h:i a', strtotime($log->created_at)) !!}</small>
                                                </div>
                                            </div>
                                            <!-- cd-timeline__content -->
                                        </div>
                                    @break
                                    @case(\App\Enums\ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE)
                                        <div class="cd-timeline__block js-cd-block">
                                            <div class="cd-timeline__img cd-timeline__yellow js-cd-img {{ isset($danger)? 'bg-danger':'' }}">
                                                <i class="timeline_icon fas {{ isset($danger)? 'fa-close':'fa-hourglass-end' }}" aria-hidden="true"></i>
                                            </div>
                                           <!-- cd-timeline__img -->
                                           <div class="cd-timeline__content js-cd-content">
                                            <strong>{{__('parcelLogs.'.$log->parcel_status)}} {{ isset($danger)? $cancel:'' }}</strong><br>
                                            <span>{{__('levels.note')}}: {{$log->note}}</span><br/>

                                            <strong>{{ __('levels.created_by') }}</strong><br/>
                                            <span>{{ __('levels.name') }}: {{ $log->user->name }} </span><br/>
                                            <span>{{ __('levels.phone_number') }}: {{ $log->user->mobile }} </span><br/>

                                            <div class="cd-timeline__date">
                                                <strong>{!! dateFormat2($log->created_at) !!}</strong><br>
                                                <small>{!! date('h:i a', strtotime($log->created_at)) !!}</small>
                                            </div>
                                        </div>
                                        <!-- cd-timeline__content -->
                                        </div>
                                    @break
                                
                                    @default
                                        <div class="cd-timeline__block js-cd-block">
                                            <div class="cd-timeline__img cd-timeline__img--picture js-cd-img {{ isset($danger)? 'bg-danger':'' }}">
                                                <i class="timeline_icon fas {{ isset($danger)? 'fa-close':'fa-check' }}" aria-hidden="true"></i>
                                            </div>
                                            <!-- cd-timeline__img -->
                                            <div class="cd-timeline__content js-cd-content">
                                                <strong>{{__('parcelLogs.'.$log->parcel_status)}} {{ isset($danger)? $cancel:'' }}</strong><br>
                                                <span>{{__('levels.note')}}: {{$log->note}}</span><br/>

                                                <strong>{{ __('levels.created_by') }}</strong><br/>
                                                <span>{{ __('levels.name') }}: {{ $log->user->name }} </span><br/>
                                                <span>{{ __('levels.phone_number') }}: {{ $log->user->mobile }} </span><br/>

                                                <div class="cd-timeline__date">
                                                    <strong>{!! dateFormat2($log->created_at) !!}</strong><br>
                                                    <small>{!! date('h:i a', strtotime($log->created_at)) !!}</small>
                                                </div>
                                            </div>
                                            <!-- cd-timeline__content -->
                                        </div>
                                @endswitch
                            @endforeach
                            <div class="cd-timeline__block js-cd-block">
                                <div class="cd-timeline__img cd-timeline__img--picture js-cd-img">
                                    <i class="timeline_icon fas fa-check" aria-hidden="true"></i>
                                </div>
                                <!-- cd-timeline__img -->
                                <div class="cd-timeline__content js-cd-content">
                                    <strong>{{__('parcel.parcel_create')}}</strong><br>
                                    <span>{{__('parcel.recipient_name')}}: {{$parcel->merchant->user->name}}</span><br> 
                                    <span>{{__('parcel.sender_phone_number')}}: {{$parcel->merchant->user->mobile}}</span><br/>

                                    <div class="cd-timeline__date">
                                        <strong>{!! dateFormat2($parcel->created_at) !!}</strong><br>
                                        <small>{!! date('h:i a', strtotime($parcel->created_at)) !!}</small>
                                    </div>
                                </div>
                                <!-- cd-timeline__content -->
                            </div>
                        </div>
                    </section>
                    <!-- cd-timeline -->
                </div>
 
            </div>
        </div>
    </section>
    <!-- end timeline  -->
</div>
<!-- end wrapper  -->
@endsection()
<!-- css  -->
@push('styles')
    <link rel="stylesheet" href="{{static_asset('backend')}}/css/logs.css">
@endpush


