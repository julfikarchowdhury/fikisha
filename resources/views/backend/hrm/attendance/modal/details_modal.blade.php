 
    <div class="row">
        @if(hasPermission('attendance_delete') || hasPermission('attendance_update'))
            <div class="col-12">
                <div class="dropdown text-right">
                    <a href="#" class="dropdown-toggle text-primary" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    ...
                    </a>
                    <div class="dropdown-menu todo-dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if(hasPermission('attendance_delete'))
                        <form action="{{ route('hrm.attendance.delete',@$attendance->id) }}"  method="post" >
                            @csrf
                            @method('delete')
                            <button type="submit" class="dropdown-item" >
                                {{ __('levels.delete') }}
                            </button>
                        </form>
                        @endif
                        @if(hasPermission('attendance_update'))
                        <a href="#"  class="dropdown-item modalBtn"   data-bs-target="#dynamic-modal"  data-title="{{ __('parcel.edit_attendance') }}" data-url="{{ route('hrm.attendance.edit.modal',['id'=>$attendance->id]) }}" >
                            {{ __('levels.edit') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="row mb-3">
        <div class="col-lg-6 text-center"> 
            <div class="row d-flex text-center align-items-center h-100">
                <div class="col-12 text-center">
                    <img src="{{ @$attendance->user->image }}" width="60"/>
                    <div class="mt-3  ">
                        <strong>{{@$attendance->user->name}}</strong> <br/>
                        <span>Date : {{ \Carbon\Carbon::parse($attendance->date)->format('d-m-Y') }}</span>
                    </div>
                </div>
            
                
            </div> 
        </div>
 
        <div class="col-6  mt-3">
           <div class="row">       
               <div class="col-6">
                   <label for="check_in" class="form-label">{{ __('parcel.check_in') }} </label><br/>
                   <label>{{ \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }}</label>
               </div>
               <div class="col-6">
                   <label for="check_out" class="form-label">{{ __('parcel.check_out') }} </label><br/>
                   <label>
                        @if($attendance->status == \App\Enums\AttendanceStatus::CHECK_IN)
                            {{ __('parcel.did_not_check_out') }}
                        @else
                            {{ \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') }}
                        @endif
                    </label>
               </div>

               <div class="col-6  mt-3 ">
                    <div class="rounded-circle stay-time">
                        <label>
                            @if($attendance->status == \App\Enums\AttendanceStatus::CHECK_IN)
                                {{ __('parcel.did_not_check_out') }}
                            @else
                                {{  @$attendance->staytime}}
                            @endif
                        </label>
                    </div>
                </div> 

           </div>
        </div> 

    </div>

