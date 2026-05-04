@extends('backend.partials.master')
@section('title')
    Mail Settings
@endsection

@section('maincontent')
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">{{ __('menus.dashboard') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="#" class="breadcrumb-link">{{ __('menus.settings') }}</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Mail Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">Mail (SMTP) Configuration</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('mail-settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="mailer">Mailer <span class="text-danger">*</span></label>
                                        <select id="mailer" name="mailer" class="form-control @error('mailer') is-invalid @enderror" required>
                                            <option value="smtp" @selected(old('mailer', $mail['mailer']) === 'smtp')>SMTP</option>
                                        </select>
                                        @error('mailer')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="host">SMTP Host <span class="text-danger">*</span></label>
                                        <input id="host" type="text" name="host" class="form-control @error('host') is-invalid @enderror"
                                            value="{{ old('host', $mail['host']) }}" placeholder="smtp.example.com" required>
                                        @error('host')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="port">SMTP Port <span class="text-danger">*</span></label>
                                        <input id="port" type="number" name="port" class="form-control @error('port') is-invalid @enderror"
                                            value="{{ old('port', $mail['port']) }}" placeholder="587" required>
                                        @error('port')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">SMTP Username</label>
                                        <input id="username" type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                            value="{{ old('username', $mail['username']) }}" placeholder="username">
                                        @error('username')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">SMTP Password</label>
                                        <input id="password" type="text" name="password" class="form-control @error('password') is-invalid @enderror"
                                            value="{{ old('password', $mail['password']) }}" placeholder="password">
                                        @error('password')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="encryption">Encryption</label>
                                        <select id="encryption" name="encryption" class="form-control @error('encryption') is-invalid @enderror">
                                            <option value="tls" @selected(old('encryption', $mail['encryption']) === 'tls')>TLS</option>
                                            <option value="ssl" @selected(old('encryption', $mail['encryption']) === 'ssl')>SSL</option>
                                        </select>
                                        @error('encryption')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from_address">From Address <span class="text-danger">*</span></label>
                                        <input id="from_address" type="email" name="from_address" class="form-control @error('from_address') is-invalid @enderror"
                                            value="{{ old('from_address', $mail['from_address']) }}" placeholder="noreply@example.com" required>
                                        @error('from_address')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from_name">From Name <span class="text-danger">*</span></label>
                                        <input id="from_name" type="text" name="from_name" class="form-control @error('from_name') is-invalid @enderror"
                                            value="{{ old('from_name', $mail['from_name']) }}" placeholder="Fikisha" required>
                                        @error('from_name')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row pt-2">
                                <div class="col-12 text-right">
                                    <button type="submit" class="btn btn-primary">{{ __('levels.save_change') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

