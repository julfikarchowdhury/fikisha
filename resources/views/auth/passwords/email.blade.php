@include('backend.partials.header')

<!-- email verification password  -->
<div class="splash-container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card auth-boxs">
                <div class="auth-header text-center">
                    <a href="{{url('/')}}" class="auth-logo">
                        <img class="logos m-auto" src="{{ settings()->logo_image }}" alt="logo">
                    </a>
                    <span class="auth-desc">Reset Password</span>
                </div>
                <div class="auth-form mt-4">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <p class="text-muted mb-2">Don't worry, we'll send you an email to reset your password.</p>
                        <div class="form-group">
                            <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email Address">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-block btn-primary btn-xl">Send Password Reset Link</button>
                    </form>
                </div>
                <div class="card-footer text-center bg-none">
                    <span>Don't have an account? <a href="{{ route('register') }}">Sign Up</a> | <a href="{{ route('login') }}">Sign In</a></span>
                </div>
            </div>

        </div>
        <!-- <div class="col-lg-7 footer-img">
            <img class="img-responsive my-5 " src="{{ static_asset('images/default/we-courier-process.png') }}" width="100%" />
        </div> -->
    </div>
</div>
<!-- end email verification password  -->
@include('backend.partials.footer')

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