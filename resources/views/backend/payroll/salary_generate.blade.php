@extends('backend.partials.master')
@section('title')
{{ __('salary.salary_generate') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<!-- wrapper  -->
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('salary.payroll')}}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link">{{ __('salary.salary_generate') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    <div class="row">
        <!-- data table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('salary.salary_generate') }}</h4>
                    @if (hasPermission('salary_generate_create') == true)
                    <div class="col-12 col-md-6">
                        <a href="{{ route('salary.generate.create') }}" class="btn btn-primary btn-sm float-right m-2 " data-toggle="tooltip" data-placement="top"><i class="fa fa-plus"></i> {{ __('levels.add') }}</a>
                        <a href="#" class="btn btn-primary btn-sm float-right m-2" data-toggle="modal" data-target="#autogenerate"> {{ __('salary.auto_generate') }} </a>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.user') }}</th>
                                    <th>{{ __('levels.month') }}</th>
                                    <th>{{ __('levels.amount') }}</th>
                                    <th>{{ __('levels.paid_amount') }}</th>
                                    <th>{{ __('levels.due_amount') }}</th>
                                    <th>{{ __('levels.status') }}</th>
                                    <th>{{ __('levels.note') }}</th>
                                    @if (hasPermission('salary_generate_update') == true || hasPermission('salary_generate_delete') == true )
                                    <th>{{ __('levels.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($salaries as $salary)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>
                                        {{$salary->user->name}}<br />
                                        {{ $salary->user->email }}
                                    </td>
                                    <td>{{\Carbon\Carbon::parse($salary->month)->format('M Y')}}</td>
                                    <td>{{settings()->currency}}{{$salary->amount}}</td>
                                    <td>{{settings()->currency}}{{$salary->payments->sum('amount')}}</td>
                                    <td>{{settings()->currency}}{{$salary->amount - $salary->payments->sum('amount')}}</td>
                                    <td>
                                        {!! $salary->my_status !!}
                                    </td>
                                    <td>
                                        {{ $salary->note }}
                                    </td>
                                    @if (hasPermission('salary_generate_update') == true || hasPermission('salary_generate_delete') == true )
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">

                                                @if (hasPermission('salary_generate_update') == true )
                                                <a href="{{ route('salary.generate.edit',$salary->id) }}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                @endif
                                                @if ( hasPermission('salary_generate_delete') == true )
                                                <form id="delete" value="Test" action="{{ route('salary-generate.delete',$salary->id) }}" method="POST" data-title="{{ __('delete.salary') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <input type="hidden" name="" value="Packaging" id="deleteTitle">
                                                    <button type="submit" class="dropdown-item"><i class="fa fa-trash" aria-hidden="true"></i> {{ __('levels.delete') }}</button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $salaries->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $salaries->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $salaries->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $salaries->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
            </div>
            @include('backend.payroll.generate')
        </div>
    </div>
    <!-- end data table  -->
</div>
</div>
<!-- end wrapper  -->
@endsection()