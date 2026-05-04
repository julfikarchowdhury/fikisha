@include('backend.partials.header')

<form class="splash-container" method="POST" action="{{ route('customer.sign-up-store') }}">
    @csrf
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-7">
            <div class="card">
                <div class="card-header text-center">
                    <a href="{{ url('/') }}">
                        <img class="logo-img" src="{{ settings()->logo_image }}" alt="logo">
                    </a>
                    <span class="splash-description d-block mt-2">Create your merchant account</span>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Step 1 of 3: Enter your email or mobile number. We will send an OTP for verification.
                    </p>

                    <div class="form-group">
                        <label for="contact">Email or Mobile Number <span class="text-danger">*</span></label>
                        <input id="contact" type="text" class="form-control form-control-lg @error('contact') is-invalid @enderror"
                            name="contact" value="{{ old('contact', $request->phone ?? '') }}" placeholder="example@email.com or 2557XXXXXXXX" autofocus>
                        @error('contact')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">Send OTP</button>
                </div>
                <div class="card-footer bg-white text-center">
                    <p class="mb-0">Already member? <a href="{{ route('login') }}" class="text-primary">Login Here.</a></p>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.title = '{{ settings()->name }} | Registration';
</script>

@include('backend.partials.footer')
