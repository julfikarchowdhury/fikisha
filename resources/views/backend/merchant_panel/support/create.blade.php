@extends('backend.partials.master')
@section('title')
    {{ __('support.supprot') }} {{ __('levels.add') }}
@endsection
@section('maincontent')
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('merchant-panel.support.index') }}" class="breadcrumb-link">{{ __('support.supprot') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.create') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    <div class="row">
        <!-- basic form -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('support.supprot_add') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{route('merchant-panel.support.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="service">{{ __('support.service') }}</label> <span class="text-danger">*</span>
                                    <select id="service" name="service" class="form-control @error('service') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('service') ? '' : 'selected' }}>{{ __('merchantPlaceholder.service') }}</option>
                                        @foreach(trans('SalaryService') as $key => $value)
                                            <option value="{{ $key }}"{{(old('service') == $key) ? 'selected' : ''}}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('service')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority">{{ __('support.priority') }}</label> <span class="text-danger">*</span>
                                    <select id="priority" name="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('priority') ? '' : 'selected' }}>{{ __('merchantPlaceholder.priority') }}</option>
                                        <option value="low"{{(old('priority') == 'low') ? 'selected' : ''}}>Low</option>
                                        <option value="medium"{{(old('priority') == 'medium') ? 'selected' : ''}}>Medium</option>
                                        <option value="high"{{(old('priority') == 'high') ? 'selected' : ''}}>High</option>
                                    </select>
                                    @error('priority')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">{{ __('support.date')}}</label> <span class="text-danger">*</span>
                                    <input id="date" type="date" name="date" class="form-control" value="{{old('date',date('Y-m-d'))}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department_id">{{ __('support.department_id') }}</label> <span class="text-danger">*</span>
                                    <select class="form-control" id="department_id" name="department_id" required>
                                        <option value="" disabled {{ old('department_id') ? '' : 'selected' }}>{{ __('merchantPlaceholder.department') }}</option>
                                        @foreach($departments as $department)
                                            <option value="{{$department->id}}" {{(old('department_id') == $department->id) ? 'selected' : ''}}>{{$department->title}}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="subject">{{ __('support.subject') }}</label> <span class="text-danger">*</span>
                                    <input id="subject" type="text" name="subject" placeholder="{{ __('merchantPlaceholder.subject') }}" autocomplete="off" class="form-control" value="{{old('subject')}}" required>
                                    @error('subject')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="attached_file">{{ __('support.attached') }}</label>
                                    <input id="attached_file" type="file" name="attached_file" autocomplete="off" class="form-control">
                                    @error('attached_file')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">{{ __('support.description')}}</label>
                            <div class="form-control-wrap user-search">
                                <textarea class="form-control" name="description" rows="5" id="description">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                <a href="{{ route('merchant-panel.support.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end basic form -->
    </div>
</div>
@endsection()

