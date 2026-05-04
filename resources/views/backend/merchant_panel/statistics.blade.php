@extends('backend.partials.master')
@section('title')
    {{ __('merchant.statistics') }} {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="javascript:void(0)" class="breadcrumb-link">{{ __('merchant.statistics') }}</a></li> 
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
           
            <div class="row p-0 mb-3">
                <div class="col-12 col-md-6">
                    <p class="h3 d-inline">{{ __('merchant.statistics') }}</p>
                </div>
                <div class="col-12 col-md-6 text-right">
                    <form action="{{ route('merchant.panel.statistics') }}" method="get" class="d-flex justify-content-end">
                     
                        <input type="hidden" value="custom" name="days"/>
                        <input type="text" autocomplete="off" id="date" name="filter_date" class=" form-control  py-1 w-50 date_range_picker" value="{{ isset($request->filter_date) ? $request->filter_date : old('filter_date') }}" placeholder="{{ __('merchantPlaceholder.date') }}">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> {{ __('levels.filter') }}</button>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 ">
                    <div class="card border-3 border-top border-top-primary analytics-col-card">
                        <div class="card-body"> 
                            <div class="row"> 
                                <div class=" col-sm-4 p-3  justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <label class="icon"><i class="fa fa-shipping-fast"></i></label>
                                        </div>
                                        <div class="ms-2 w-100">
                                            <h5 class="text-muted mx-3 fontSize-30 mb-0">{{ __('dashboard.deliverd') }}</h5>
                                            <h1 class="mx-3 fontSize-30 mb-0">{{$td_delivered}}</h1>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-sm-4 p-3 justify-content-between align-items-center">
                                    <div class="d-flex  align-items-center"> 
                                        <div>
                                            <label class="icon"><i class="fa fa-dna  "></i></label>
                                        </div>
                                        <div class="ms-2 w-100">
                                            <h5 class="text-muted mx-4 fontSize-30 mb-0">{{ __('dashboard.return') }}</h5>
                                            <h1 class="mx-3 fontSize-30 mb-0">{{$t_returned_merchant}}</h1>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-sm-4 p-3 justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <label class="icon"><i class="fa fa-credit-card"></i></label>
                                        </div>
                                        <div class="w-100 ms-2">
                                            <h5 class="text-muted mx-3 fontSize-30 mb-0">{{ __('dashboard.current_balance') }}</h5>
                                            <h1 class=" mx-3 fontSize-15 mb-0"s style="font-size: 25px">{{ @settings()->currency }} {{ $merchant->current_balance }}</h1>
                                        </div>
                                    </div>
                                </div> 
                            </div>

                            <div class="row analytics-multicard mt-3">
                                  <div class="col-xl-6">
                                      <div class="card bg-primary  ">
                                          <div class="card-body">
                                              <div class="d-flex my-2  justify-content-between">
                                                   <div class="text-dark">{{ __('dashboard.deliverd') }}</div>
                                                   <div class="text-dark"> {{ @$total_delivered_count }}</div>
                                              </div>
                                              <div class="d-flex  my-2 justify-content-between">
                                                   <div class="text-dark">{{ __('dashboard.collected_amount') }}</div>
                                                   <div class="text-dark">{{ @settings()->currency }}  {{ @(int)$t_delivered_collected_amount }}</div>
                                              </div>
                                              <div class="d-flex  my-2 justify-content-between">
                                                   <div class="text-dark">{{ __('dashboard.delivery_charges') }}</div>
                                                   <div class="text-dark">{{ @settings()->currency }}  {{ @(int)$delivered_delivery_charge }}</div>
                                              </div>
                                              <div class="d-flex  my-2 justify-content-between">
                                                   <div class="text-dark">{{ __('dashboard.cod') }}</div>
                                                   <div class="text-dark">{{ @settings()->currency }}  {{ @(int)$delivered_cod }}</div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-xl-6">
                                      <div class="card bg-primary ">
                                          <div class="card-body">
                                                <div class="d-flex my-2  justify-content-between">
                                                    <div class="text-dark">{{ __('dashboard.failure_delivered') }}</div>
                                                    <div class="text-dark"> {{ @$total_par_delivered_count }}</div>
                                                </div>
                                                <div class="d-flex  my-2 justify-content-between">
                                                    <div class="text-dark">{{ __('dashboard.collected_amount') }}</div>
                                                    <div class="text-dark">{{ @settings()->currency }}  {{  @(int)$t_par_delivered_collected_amount }}</div>
                                                </div>
                                                <div class="d-flex  my-2 justify-content-between">
                                                    <div class="text-dark">{{ __('dashboard.delivery_charges') }}</div>
                                                    <div class="text-dark">{{ @settings()->currency }}  {{ @(int)$par_delivered_delivery_charge }}</div>
                                                </div>
                                                <div class="d-flex  my-2 justify-content-between">
                                                    <div class="text-dark">{{ __('dashboard.cod') }}</div>
                                                    <div class="text-dark">{{ @settings()->currency }}  {{ @(int)$par_delivered_cod }}</div>
                                                </div>
                                          </div>
                                      </div>
                                  </div> 
                            </div>
                        </div>  
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-3 border-top border-top-primary analytics-col-card">
                        <div class="card-body">
                            <div class="row"> 
                                <div class="col-sm-4 p-3 justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <label class="icon"><i class="fa fa-hourglass-half"></i></label>
                                        </div>
                                        <div class="w-100 ms-2"> 
                                            <h5 class="text-muted mx-3 fontSize-30 mb-0">{{ __('dashboard.unassigned') }}</h5>
                                            <h1 class="mx-3 fontSize-30 mb-0">{{$t_pending}}</h1> 
                                        </div>
                                    </div> 
                                </div>
                                <div class="col-sm-4 p-3 justify-content-between align-items-center">
                                    <div class="d-flex  align-items-center">  
                                        <div>
                                            <label class="icon"><i class="fa fa-dolly"></i></label>
                                        </div>
                                        <div class="w-100 ms-2">
                                            <h5 class="text-muted mx-3 fontSize-30 mb-0">{{ __('dashboard.processing') }}</h5>
                                            <h1 class="mx-3 fontSize-30 mb-0">{{$t_in_transit}}</h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 p-3 justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <label class="icon"><i class="fas fa-search-dollar"></i></label>
                                        </div>
                                        <div class="w-100 ms-2">
                                            <h5 class="text-muted mx-3 fontSize-30 mb-0">{{ __('dashboard.unassigned_amount') }}</h5>
                                            <h1 class="mx-3 fontSize-30 mb-0">{{ @settings()->currency }} {{(int)$t_balance_pending}}</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row analytics-multicard mt-3">
                                <div class="col-xl-4">
                                    <div class="card bg-primary">
                                        <div class="card-body">
                                            <div class="text-center">
                                                 <div class="text-dark font-size-20px">{{ __('dashboard.inside_dhaka') }}</div> 
                                            </div>
                                            <div class="d-flex my-2  justify-content-between">
                                                 <div class="text-dark">{{ __('dashboard.parcel') }}</div>
                                                 <div class="text-dark"> {{ @$inside_dhaka_parcel_count }}</div>
                                            </div>
                                            <div class="d-flex  my-2 justify-content-between">
                                                 <div class="text-dark">{{ __('dashboard.amount') }}</div>
                                                 <div class="text-dark">{{ @settings()->currency }}  {{ @(int)$inside_dhaka_parcel_amount }}</div>
                                            </div>
                                            <div class="d-flex  my-2 justify-content-between">
                                                 <div class="text-dark">{{ __('dashboard.delivery_charges') }}</div>
                                                 <div class="text-dark">{{ @settings()->currency }}  {{ @(int)$inside_dhaka_parcel_delivery_charge }}</div>
                                            </div> 
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-xl-4">
                                    <div class="card bg-primary">
                                        <div class="card-body">
                                            <div class="text-center font-size-20px">
                                                <div class="text-dark">{{ __('dashboard.outside_dhaka') }}</div> 
                                           </div>  
                                           <div class="d-flex my-2  justify-content-between">
                                                <div class="text-dark">{{ __('dashboard.parcel') }}</div>
                                                <div class="text-dark"> {{ @$outside_dhaka_parcel_count }}</div>
                                            </div>
                                            <div class="d-flex  my-2 justify-content-between">
                                                <div class="text-dark">{{ __('dashboard.amount') }}</div>
                                                <div class="text-dark">{{ @settings()->currency }}  {{ @(int)$outside_dhaka_parcel_amount }}</div>
                                            </div>
                                            <div class="d-flex  my-2 justify-content-between">
                                                <div class="text-dark">{{ __('dashboard.delivery_charges') }}</div>
                                                <div class="text-dark">{{ @settings()->currency }}  {{ @(int)$outside_dhaka_parcel_delivery_charge }}</div>
                                            </div> 
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-xl-4">
                                    <div class="card bg-primary">
                                        <div class="card-body">
                                            <div class="text-center font-size-20px;">
                                                <div class="text-dark">{{ __('dashboard.last_24_hours') }}</div> 
                                           </div>
                                            <div class="d-flex my-2  justify-content-between">
                                                <div class="text-dark">{{ __('dashboard.parcel') }}</div>
                                                <div class="text-dark"> {{ @$last24HParcel_count }}</div>
                                            </div>
                                            <div class="d-flex  my-2 justify-content-between">
                                                <div class="text-dark">{{ __('dashboard.amount') }}</div>
                                                <div class="text-dark">{{ @settings()->currency }}  {{ @(int)$last24HParcel_amount }}</div>
                                            </div>
                                            <div class="d-flex  my-2 justify-content-between">
                                                <div class="text-dark">{{ __('dashboard.delivery_charges') }}</div>
                                                <div class="text-dark">{{ @settings()->currency }}  {{ @(int)$last24HParcel_delivery_charge }}</div>
                                            </div> 
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <p class="h3 mt-3">{{ __('dashboard.all_reports') }}</p>
            <div class="row all-reports "> 
                <div class="col-12 col-md-6  col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body p-3 text-right">
                            <h5 class="text-muted m-0">{{ __('dashboard.total_parcel') }} </h5>
                            <div class="d-flex justify-content-between">
                                <p class="h3"><i class="fa fa-box-open font-size-25em"></i></p>
                                <p class="h3 my-3"> {{ @$t_parcel }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6  col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body p-3 text-right">
                            <h5 class="text-muted m-0">{{ __('dashboard.total_amount') }} </h5>
                            <div class="d-flex justify-content-between">
                                <p class="h3"><i class="fa fa-donate font-size-25em"></i></p>
                                <p class="h3 my-3">{{ @settings()->currency }} {{ @(int)$total_amount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6  col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body p-3 text-right">
                            <h5 class="text-muted m-0">{{ __('dashboard.total_return') }} </h5>
                            <div class="d-flex justify-content-between">
                                <p class="h3"><i class="fa fa-dna   font-size-25em"></i></p>
                                <p class="h3 my-3"> {{ @$t_return}}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body p-3 text-right">
                            <h5 class="text-muted m-0">{{ __('dashboard.total_returned_fees') }} </h5>
                            <div class="d-flex justify-content-between">
                                <p class="h3"><i class="fas fa-dollar-sign font-size-25em"></i></p>
                                <p class="h3 my-3">{{ @settings()->currency }} {{ @(int)$return_fees }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6  col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body p-3 text-right">
                            <h5 class="text-muted m-0">{{ __('dashboard.total_delivered') }} </h5>
                            <div class="d-flex justify-content-between">
                                <p class="h3"><i class="fa fa-shipping-fast font-size-25em"></i></p>
                                <p class="h3 my-3"> {{ @$t_delivered }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12  col-md-6  col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body p-3 text-right">
                            <h5 class="text-muted m-0">{{ __('dashboard.delivered_amount') }} </h5>
                            <div class="d-flex justify-content-between">
                                <p class="h3"><i class="fa fa-hand-holding-usd font-size-25em"></i></p>
                                <p class="h3 my-3">{{ @settings()->currency }} {{ @(int)$delivered_amount }}</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-12 col-md-6  col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body p-3 text-right">
                            <h5 class="text-muted m-0">{{ __('dashboard.delivery_charges') }} </h5>
                            <div class="d-flex justify-content-between">
                                <p class="h3"><i class="fas fa-search-dollar font-size-25em"></i></p>
                                <p class="h3 my-3">{{ @settings()->currency }} {{ @(int)$t_delivery_fee }}</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-12 col-md-6  col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body p-3 text-right">
                            <h5 class="text-muted m-0">{{ __('dashboard.cod') }} </h5>
                            <div class="d-flex justify-content-between">
                                <p class="h3"><i class="fa fa-credit-card font-size-25em"></i></p>
                                <p class="h3 my-3">{{ @settings()->currency }} {{ @(int)$t_cod_amount }}</p>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
     

        </div>
        <!-- end data table  -->
    </div>
</div>
<!-- end wrapper  -->
@endsection()


@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@push('scripts')
 
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>

@endpush
