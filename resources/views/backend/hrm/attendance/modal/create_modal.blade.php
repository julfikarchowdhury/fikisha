<form action="{{ route('hrm.attendance.store',['user_id'=>$request->user_id,'date'=>$request->date]) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row mb-3">
        <div class="col-lg-6 text-center"> 
            <div class="row d-flex text-center align-items-center h-100">
                <div class="col-12 text-center">
                    <img src="{{ @$user->image }}" width="60"/>
                </div>
                <div class="col-12 mt-2">
                    <strong>{{@$user->name}}</strong> 
                </div>
                <div class="col-12 ">
                    <span>Date : {{ \Carbon\Carbon::parse($request->date)->format('d-m-Y') }}</span>
                </div>
            </div> 
        </div> 

        <div class="col-lg-6">
            <div>
                <label for="check_in" class="form-label">{{ __('parcel.check_in') }} </label>
                <input type="time" name="check_in" class="form-control form--control" id="check_in" value="{{ old('check_in',date('H:i')) }}">
                @error('check_in')
                    <p class="text-danger pt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-3">
                <label for="check_out" class="form-label">{{ __('parcel.check_out') }} </label>
                <input type="time" name="check_out" class="form-control form--control" id="check_out" value="{{ old('check_out') }}">
                @error('check_out')
                    <p class="text-danger pt-2">{{ $message }}</p>
                @enderror
            </div> 
        </div> 

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">{{ __('parcel.close') }}</button>
        <button type="submit" class="btn submit-btn btn-primary btn-sm"> <i class="fa fa-save"></i> {{__('levels.save')}}</button>
      </div>
</form>
