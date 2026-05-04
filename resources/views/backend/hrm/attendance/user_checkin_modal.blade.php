@php
    $attendance = App\Models\Backend\HRM\Attendance::where(['user_id' => Auth::user()->id])
        ->whereDate('date', date('Y-m-d'))
        ->first();
@endphp
<div id="attendance-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="modalSize">
        <div class="modal-content rounded-xl p-0 b-0">
            <div class="panel panel-color panel-primary">
                <div class="modal-header">
                    <h5 class="modal-title" id="odal-title">
                        @if ($attendance && empty($attendance->check_out))
                            {{ __('parcel.check_out') }}
                        @elseif($attendance && !empty($attendance->check_out))
                            {{ __('levels.details') }}
                        @else
                            {{ __('parcel.check_in') }}
                        @endif
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    @if ($attendance && empty($attendance->check_out))
                        <div class="row mb-3">
                            <div class="col-lg-12 text-center p-3">
                                <h3 class="text-center">{{ __('parcel.check_out') }}</h3>
                                <h2 class="clock text-center"></h2>
                                <div class="button-hold-container"> 
                                    <button id="checkin" class="m-auto checkin button-hold"> 
                                        <div> 
                                            <svg class="progress" viewBox="0 0 32 32"> 
                                                <circle r="8" cx="16" cy="16"></circle>
                                            </svg>
                                            <svg class="tick" viewBox="0 0 32 32">
                                                <polyline points="18,7 11,16 6,12"></polyline>
                                            </svg>
                                        </div>  
                                    </button>
                                </div> 
                            </div>
                        </div>
                        <input type="hidden" data-url="{{ route('hrm.attendance.checkout') }}" id="checkin_url" />
                    @elseif($attendance && !empty($attendance->check_out))
                        <div class="row align-items-center mb-3">
                            <div class="col-lg-6 p-3">
                                <h3>{{ __('parcel.check_in') }} : <span
                                        class="badge badge-success">{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i A') }}</span>
                                </h3>
                                <h3>{{ __('parcel.check_out') }} : <span
                                        class="badge badge-success">{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i A') }}</span>
                                </h3>
                            </div>
                            <div class="col-lg-6 p-3">
                                <div class="rounded-circle stay-time">
                                    <label> Stay Time <br />{{ @$attendance->staytime }} </label>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row mb-3">
                            <div class="col-lg-12 text-center p-3">
                                <h3 class="text-center">{{ __('parcel.check_in') }}</h3>
                                <h2 class="clock text-center"></h2>
                                <div class="button-hold-container"> 
                                    <button id="checkin" class="m-auto checkin button-hold"> 
                                        <div> 
                                            <svg class="progress" viewBox="0 0 32 32"> 
                                                <circle r="8" cx="16" cy="16"></circle>
                                            </svg>
                                            <svg class="tick" viewBox="0 0 32 32">
                                                <polyline points="18,7 11,16 6,12"></polyline>
                                            </svg>
                                        </div>  
                                    </button>
                                </div> 
                            </div>
                        </div>
                        <input type="hidden"  id="checkin_url" />
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
 
    <style>
        .button-hold>div:before {
            content: url("{{ static_asset('/backend/images/fingerprint-solid.svg') }}");
            position: absolute;
            transform: scale(var(--background-scale, 1.5));
            z-index: 1;
            width: 40px
        }
    </style>
 