@include('backend.partials.header')
@section('content')
<div class="splash-container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card auth-boxs">
                <div class="auth-header text-center">
                    <a href="{{url('/')}}" class="auth-logo">
                        <img class="logos m-auto" src="{{ settings()->logo_image }}" alt="logo">
                    </a>
                    <span class="auth-desc">Please enter your user information.</span>
                </div>
                <div class="auth-form mt-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <input id="email" type="text" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" required autocomplete="email" autofocus placeholder="Enter Email" value="{{ old('email') }}">
                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password" value="{{ old('password') }}">
                            @error('password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 mb-3 flex-wrap">
                            <div class="form-group mb-0">
                                <label class="custom-control custom-checkbox mb-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </label>
                            </div>
                            <div class="forget">
                                <a href="{{ route('password.request') }}" class="footer-link text-primary">Forgot Password</a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>
                        @if(env('DEMO') && env('DEMO') != "")
                        <div class="text-center p-2">
                            <span><b>Demo Login</b></span>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-primary btn-lg btn-block mt-2 demo-login-btn" id="demo-admin" data-email="admin@wemaxdevs.com" data-password="12345678">Admin</button>
                            </div>
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-primary btn-lg btn-block mt-2 demo-login-btn" id="demo-branch" data-email="branch@wemaxdevs.com" data-password="12345678">Branch</button>
                            </div>
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-primary btn-lg btn-block mt-2 demo-login-btn" id="demo-merchant" data-email="merchant@wemaxdevs.com" data-password="12345678">Merchant</button>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
                <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap mt-3">
                    <p class="d-inline-block mb-0 text-muted">Don't have an account?</p>
                    <div class="link">
                        <a href="{{ route('customer.sign-up') }}" class="footer-link text-primary">Sign up here</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@show


<style lang="scss" scoped>
    .login-dashboard-main-wrapper {
        padding-top: 10% !important;
    }

    .text-primary {
        color: var(--bs-primary) !important;
        text-decoration: none !important;

        &:hover {
            color: var(--bs-primary) !important;
        }
    }

    .text-muted {
        color: #666 !important;
    }

    .auth-boxs {
        max-width: 500px;
        width: 100%;
        margin: 0 auto;
        background: #fff;
        border-radius: 10px;
        padding: 40px;
        box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);

        .auth-header {
            .auth-logo {
                img {
                    width: auto;
                    height: 40px;
                    display: block;
                    margin-bottom: 14px !important;
                }
            }

            .auth-desc {
                font-size: 16px;
                color: #666;
                margin-top: 10px;
            }
        }

        .btn {
            padding: 8px 20px !important;
        }
    }
</style>
<script>
    var siteTitle = '{{ settings()->name }} | {{ __("levels.login") }}';
    document.title = siteTitle;
</script>
@include('backend.partials.footer')