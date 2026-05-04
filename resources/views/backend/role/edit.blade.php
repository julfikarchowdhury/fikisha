@extends('backend.partials.master')
@section('title')
   {{ __('role.title') }} {{ __('levels.edit') }}
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
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.edit') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('role.edit_role') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.update',['id' => $role->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @php($selectedPermissions = collect(old('permissions', $role->permissions ?? [])))
                        <div class="row">
                            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 mb-3">
                                <div class="card border role-meta-card">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0">Role Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="name">{{ __('levels.name') }}</label> <span class="text-danger">*</span>
                                            <input id="name" type="text" name="name" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" required>
                                            @error('name')
                                                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-0">
                                            <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                                            <select name="status" class="form-control @error('status') is-invalid @enderror">
                                                @foreach(trans('status') as $key => $status)
                                                    <option value="{{ $key }}" {{ (old('status', $role->status) == $key) ? 'selected' : '' }}>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="card border permission-manager-card">
                                    <div class="card-header bg-white">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" id="checkPermissionAll">
                                                <label class="form-check-label font-weight-600" for="checkPermissionAll">
                                                    Select All Marketplace Permissions
                                                </label>
                                            </div>
                                            <span class="badge badge-light" id="selectedPermissionCount">0 selected</span>
                                        </div>
                                        <div class="mt-2">
                                            <input type="text" id="permissionSearch" class="form-control form-control-sm" placeholder="Search module or permission...">
                                        </div>
                                    </div>
                                    <div class="card-body permission-scroll">
                                        @php($permission_groups = 0)
                                        @php($all_permissions = 0)
                                        @php($marketplacePermissionAttributes = [
                                            'dashboard',
                                            'roles',
                                            'designations',
                                            'departments',
                                            'users',
                                            'merchant',
                                            'parcel',
                                            'delivery_man',
                                            'delivery_charge',
                                            'reports',
                                            'support',
                                            'dispute',
                                            'platform_ledger',
                                            'sms_settings',
                                            'sms_send_settings',
                                            'general_settings',
                                            'payout_setup_settings',
                                            'parcel_category',
                                            'provinces',
                                            'city',
                                            'social_link',
                                            'services',
                                            'slider',
                                            'why_courier',
                                            'faq',
                                            'partner',
                                            'pages',
                                            'sections',
                                        ])
                                        @foreach ($permissions as $keyMain => $permission)
                                            @if (in_array($permission->attribute, $marketplacePermissionAttributes, true))
                                                @php($groupClass = 'permission-group-' . $permission->attribute)
                                                @php($moduleLabel = __('permissions.' . $permission->attribute))
                                                <div class="permission-module border rounded mb-3 p-3" data-module="{{ strtolower($moduleLabel) }}">
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div class="form-check mb-2 mb-md-0">
                                                            <input
                                                                class="form-check-input group-master-checkbox"
                                                                type="checkbox"
                                                                id="groupMaster{{ $keyMain+1 }}"
                                                                data-group="{{ $groupClass }}"
                                                                onclick="checkPermissionByGroup('{{ $groupClass }}', this)"
                                                            >
                                                            <label class="form-check-label font-weight-600" for="groupMaster{{ $keyMain+1 }}">{{ $moduleLabel }}</label>
                                                        </div>
                                                        <small class="text-muted">{{ count($permission->keywords) }} permissions</small>
                                                    </div>
                                                    <div class="row mt-3">
                                                        @foreach ($permission->keywords as $key => $keyword)
                                                            @php($keywordLabel = __('permissions.' . $key))
                                                            <div class="col-md-6 mb-2 permission-item" data-keyword="{{ strtolower($keywordLabel) }}">
                                                                <div class="form-check permission-pill">
                                                                    <input
                                                                        id="checkPermission{{ $keyword }}"
                                                                        class="form-check-input common-key permission-item-checkbox {{ $groupClass }}"
                                                                        type="checkbox"
                                                                        onclick="checkPermissionByGroupOne('{{ $groupClass }}', this)"
                                                                        value="{{ $keyword }}"
                                                                        name="permissions[]"
                                                                        {{ $selectedPermissions->contains($keyword) ? 'checked' : '' }}
                                                                    />
                                                                    <label class="form-check-label" for="checkPermission{{ $keyword }}">{{ $keywordLabel }}</label>
                                                                </div>
                                                            </div>
                                                            @php($all_permissions += 1)
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @php($permission_groups += 1)
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
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
    .form-check-inline .form-check-input,
    .form-check .form-check-input {
        position: static;
        margin-top: 0;
        margin-right: .3125rem;
        margin-left: 0;
        height: 20px;
        width: 20px;
    }

    .role-meta-card,
    .permission-manager-card {
        border-color: #e9edf5 !important;
        box-shadow: 0 8px 20px rgba(20, 35, 60, 0.04);
    }

    .permission-scroll {
        max-height: 560px;
        overflow-y: auto;
    }

    .permission-module {
        background: #fcfdff;
        border-color: #e8edf4 !important;
    }

    .permission-pill {
        background: #fff;
        border: 1px solid #edf1f7;
        border-radius: 8px;
        padding: 8px 10px;
    }

    .font-weight-600 {
        font-weight: 600;
    }
</style>
@endpush
@push('scripts')
<script>
    $("#checkPermissionAll").on('click', function() {
        const checked = $(this).is(':checked');
        $('.group-master-checkbox, .permission-item-checkbox').prop('checked', checked);
        updateSelectedCount();
    });

    function checkPermissionByGroup(groupClassName, checkThis) {
        const checked = $("#" + checkThis.id).is(':checked');
        $('.permission-item-checkbox.' + groupClassName).prop('checked', checked);
        implementAllChecked();
        updateSelectedCount();
    }

    function checkPermissionByGroupOne(groupClassName, checkThis) {
        const classCheckBox = $('.permission-item-checkbox.' + groupClassName);
        implementAllChecked();
        checkSinglePermission(groupClassName, classCheckBox.length);
        updateSelectedCount();
    }

    function checkSinglePermission(groupClassName, countTotalPermission) {
        const checkedItems = $('.permission-item-checkbox.' + groupClassName + ':checked').length;
        const groupMaster = $('.group-master-checkbox[data-group="' + groupClassName + '"]');
        groupMaster.prop('checked', checkedItems === countTotalPermission);
    }

    function implementAllChecked() {
        const countPermissions = {{ $all_permissions }};
        const countPermissionGroups = {{ $permission_groups }};
        if ($('.permission-item-checkbox:checked').length === countPermissions &&
            $('.group-master-checkbox:checked').length === countPermissionGroups) {
            $("#checkPermissionAll").prop('checked', true);
        } else {
            $("#checkPermissionAll").prop('checked', false);
        }
    }

    function updateSelectedCount() {
        const selected = $('.permission-item-checkbox:checked').length;
        $('#selectedPermissionCount').text(selected + ' selected');
    }

    function initializeGroupChecks() {
        $('.group-master-checkbox').each(function () {
            const groupClassName = $(this).data('group');
            const total = $('.permission-item-checkbox.' + groupClassName).length;
            checkSinglePermission(groupClassName, total);
        });
        implementAllChecked();
        updateSelectedCount();
    }

    $('#permissionSearch').on('keyup', function () {
        const keyword = ($(this).val() || '').toLowerCase().trim();
        $('.permission-module').each(function () {
            const moduleName = ($(this).data('module') || '').toString();
            let hasVisibleItem = false;

            $(this).find('.permission-item').each(function () {
                const permissionName = ($(this).data('keyword') || '').toString();
                const matched = keyword === '' || moduleName.includes(keyword) || permissionName.includes(keyword);
                $(this).toggle(matched);
                if (matched) {
                    hasVisibleItem = true;
                }
            });

            $(this).toggle(hasVisibleItem || moduleName.includes(keyword));
        });
    });

    $(document).ready(function () {
        initializeGroupChecks();
    });
</script>
@endpush

