@extends('backend.partials.master')
@section('title')
   {{ __('role.title') }} {{ __('levels.clone') }}
@endsection
@section('maincontent')
<div class="container-fluid  dashboard-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('menus.user_role')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}" class="breadcrumb-link">{{ __('role.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.clone') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('role.title') }} {{ __('levels.clone') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('role.title') }} {{ __('levels.clone') }}</h2> -->
                    <form action="{{route('roles.store.clone')}}"  method="POST" enctype="multipart/form-data" id="basicform">
                        @csrf
                        <div class="row">
                            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="name">{{ __('levels.name') }}</label> <span class="text-danger">*</span>
                                    <input id="name" type="text" name="name" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$role->name) }}" require>
                                    @error('name')
                                        <span class="text-danger mt-2">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        @foreach(trans('status') as $key => $status)
                                            <option value="{{ $key }}" {{ (old('status',$role->status) == $key) ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
                                <table class="table border  permission-table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="checkPermissionAll">
                                                    <label class="form-check-label" for="checkPermissionAll">{{ __('permissions.modules') }}</label>
                                                </div>
                                            </th>
                                            <th class="text-left">{{ __('permissions.permissions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($permission_groups = 0)
                                        @php($all_permissions = 0)
                                        @foreach ($permissions as $keyMain => $permission)
                                            <tr>
                                                <td>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input role-{{ $keyMain+1 }}-management-checkbox " type="checkbox" id="{{ $keyMain+1 }}Management" value="{{ $permission->attribute }}" onclick="checkPermissionByGroup('role-{{ $keyMain+1 }}-management-checkbox', this)" @if(count($permission->keywords) == checkPermission($permission->keywords, $role->permissions)) checked @endif>
                                                        <label class="form-check-label" for="checkPermission">{{__('permissions.'.$permission->attribute) }}</label>
                                                    </div>
                                                </td>
                                                <td class="text-left">
                                                    @foreach ($permission->keywords as $key => $keyword)
                                                        <div class="row align-items-center justify-content-start permission-check-box pb-2 pt-2 pl-4" >
                                                            <input id="{{ $keyword }}" class="read common-key form-check-input" type="checkbox" onclick="checkPermissionByGroupOne('role-{{ $keyMain+1 }}-management-checkbox', this)" value="{{ $keyword }}" name="permissions[]"
                                                             @if($role->permissions !==null && in_array($keyword, $role->permissions)) checked @endif
                                                             />
                                                            <label for="{{ $keyword }}">{{ __('permissions.'.$key) }}</label>
                                                        </div>
                                                        @php($all_permissions += 1)
                                                    @endforeach
                                                </td>
                                            </tr>
                                            @php($permission_groups += 1)
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 text-right ">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save_change') }}</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection()
@push('styles')
<style>
    .form-check-inline .form-check-input {
        position: static;
        margin-top: 0;
        margin-right: .3125rem;
        margin-left: 0;
        height: 20px;
        width: 20px;
    }
</style>
@endpush
@push('scripts')
   
    <script>
        /**
         * Check all the permissions
         */
        $("#checkPermissionAll").click(function() {
            if ($(this).is(':checked')) {
                // check all the checkbox
                $('input[type=checkbox]').prop('checked', true);
            } else {
                // un check all the checkbox
                $('input[type=checkbox]').prop('checked', false);
            }
        });

        function checkPermissionByGroup(className, checkThis) {
            const groupIdName = $("#" + checkThis.id);
            const classCheckBox = $('.' + className + ' input');
            if (groupIdName.is(':checked')) {
                classCheckBox.prop('checked', true);
            } else {
                classCheckBox.prop('checked', false);
            }
            implementAllChecked();
        }

        function checkPermissionByGroupOne(className, checkThis) {
            const groupIdName = $("#" + checkThis.id);
            const classCheckBox = $('.' + className + ' input');
            implementAllChecked();
            checkSinglePermission(className, checkThis.id, classCheckBox.length);
        }

        function checkSinglePermission(groupClassName, groupID, countTotalPermission) {
            const classCheckbox = $('.' + groupClassName + ' input');
            const groupIDCheckBox = $("." + groupClassName);
            // if there is any occurance where something is not selected then make selected = false
            if ($('.' + groupClassName + ' input:checked').length == countTotalPermission) {
                groupIDCheckBox.prop('checked', true);
            } else {
                groupIDCheckBox.prop('checked', false);
            }
            implementAllChecked();
        }

        function implementAllChecked() {
            const countPermissions = {{ $all_permissions }};
            const countPermissionGroups = {{ $permission_groups }};
            if ($('input[type="checkbox"]:checked').length >= (countPermissions + countPermissionGroups)) {
                $("#checkPermissionAll").prop('checked', true);
            } else {
                $("#checkPermissionAll").prop('checked', false);
            }
        }

        showAllChecked();
        function showAllChecked() {
            const countPermissions = {{ $all_permissions }};
            if ($('.permission-check-box input[type="checkbox"]:checked').length == countPermissions) {
                $("#checkPermissionAll").prop('checked', true);
            } else {
                $("#checkPermissionAll").prop('checked', false);
            } 
        }
    </script>
@endpush

