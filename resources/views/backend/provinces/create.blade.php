@extends('backend.partials.master')
@section('title')
    {{ __('city.title') }} {{ __('levels.add') }}
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
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                        class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('departments.index') }}"
                                        class="breadcrumb-link">{{ __('levels.province') }}</a></li>
                                <li class="breadcrumb-item"><a href=""
                                        class="breadcrumb-link active">{{ __('levels.create') }}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- end pageheader -->
        <div class="row">
            <!-- basic form -->
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.create') }} {{ __('levels.province') }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- <h2 class="pageheader-title">{{ __('levels.create') }} {{ __('levels.province') }}</h2> -->
                            <div id="bulk-import"></div>
                            <div class="row mt-3">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                    <button type="button" id="importBtn" class="btn btn-space btn-primary me-2">{{ __('levels.submit') }}</button>
                                    <button type="button" id="clearTable" class="btn btn-space btn-warning mb-0">{{ __('levels.clear') }}</button> 
                                    <a href="{{ route('province.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                                </div>
                            </div>
                    
                    </div>
                </div>
            </div>
            <!-- end basic form -->
        </div>
    </div>
@endsection
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ static_asset('backend/vendor/handsontable') }}/handsontable.full.min.css">  
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ static_asset('backend/vendor/handsontable') }}/handsontable.full.min.js"></script>
 
    <script type="text/javascript">
        const container = document.querySelector('#bulk-import');
        const generateData = (rows = 13, columns = 4, additionalRows = true) => {

            const array2d = [...new Array(rows)]
                .map(_ => [...new Array(columns)]
                    .map(_ => ''));

            if (additionalRows) {
                array2d.push([]);
                array2d.push([]);
            }
            return array2d;
        };
     
        function nameValidator(value, callback) {
            var names = value.toString().length;
            if (names >= 1) {
                callback(true);
            } else {
                callback(false);
            }
        }
 
        const hot = new Handsontable(container, {
            data: generateData(),
            columns: [{
                    validator: nameValidator,
                    width: '100'
                },
                {
                    width: '100',
                    validator: nameValidator,
                },
                {
                    width: '100',
                    type: 'numeric',
                },
                { 
                    width: '100'
                }
                 
            ],
            colHeaders: ['Name *', 'State code *',  'Position','Description'],

            width: '100%',
            height: 'auto',
            rowHeaders: true,
            stretchH: 'all', // 'none' is default
            contextMenu: true,
            activeHeaderClassName: 'ht__active_highlight',
            licenseKey: 'non-commercial-and-evaluation',

        });

        function clearTable(hot){
            const numRows = hot.countRows();
            const numCols = hot.countCols();
            const newData = Array.from({ length: numRows }, () => Array(numCols).fill(null)); // Fill with nulls or default values 
            hot.loadData(newData); 
        }
        $('#clearTable').click(function(){
            clearTable(hot);
        });

        $('#importBtn').on('click', function() {
            $(this).html('Loading...');
            $(this).attr('disabled', 'disabled');
            var data = hot.getData();
            $.ajax({
                url: "{{ route('province.store') }}",
                method: 'post',
                dataType: 'json',
                data: {
                    data: data
                },
                success: (response) => {
                    $(this).html('submit');
                    $(this).removeAttr('disabled');

                    if (response.success == null) {
                        toastr.error("{{ __('levels.no_state_has_been_placed_yet') }}",
                            'Error');
                    } else if (response.success == true) {
                        toastr.success("{{ __('levels.state_import_successfully') }}", 'Success');
                        clearTable(hot);
                    } else {
                        toastr.error("{{ __('parcel.error_msg') }}", "Error");
                    }
                },
                error: (error) => {
                    toastr.error("{{ __('parcel.error_msg') }}", "Error");
                    $(this).html('Submit');
                    $(this).removeAttr('disabled');
                }
            })
        });
    </script>
@endpush
