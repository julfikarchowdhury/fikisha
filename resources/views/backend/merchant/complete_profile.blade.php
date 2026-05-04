@include('backend.partials.header')

<form class="splash-container" method="POST" action="{{ route('customer.complete-profile') }}">
    @csrf
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-7">
            <div class="card">
                <div class="card-header text-center">
                    <a href="{{ url('/') }}">
                        <img class="logo-img" src="{{ settings()->logo_image }}" alt="logo">
                    </a>
                    <span class="splash-description d-block mt-2">Complete your profile</span>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Step 3 of 3: Enter your basic profile details to activate your account.
                    </p>

                    <div class="form-group">
                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                        <input id="first_name" type="text" class="form-control form-control-lg @error('first_name') is-invalid @enderror"
                            name="first_name" value="{{ old('first_name') }}" placeholder="First Name *" autofocus>
                        @error('first_name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                        <input id="last_name" type="text" class="form-control form-control-lg @error('last_name') is-invalid @enderror"
                            name="last_name" value="{{ old('last_name') }}" placeholder="Last Name *">
                        @error('last_name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                            name="password" placeholder="Password *">
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">Complete Registration</button>
                </div>
            </div>
        </div>
    </div>
</form>

@include('backend.partials.footer')
