@extends('backend.partials.master')
@section('title')
   {{ __('role.title') }} {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('role.title') }}</h4>
                    @if(hasPermission('role_create') == true)
                    <a href="{{route('roles.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table   " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.name') }}</th>
                                    <th>{{ __('levels.slug') }}</th>
                                    <th>{{ __('levels.permission') }}</th>
                                    <th>{{ __('levels.status') }}</th>

                                    @if(hasPermission('role_update') == true || hasPermission('role_delete') == true)
                                    <th>{{ __('levels.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($roles as $role)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$role->name}}</td>
                                    <td>{{$role->slug}}</td>
                                    <td>
                                        @if(!empty($role->permissions) )
                                                <label class="badge badge-primary">{{ count($role->permissions) }}</label>
                                        @endif
                                    </td>
                                    <td>{!! $role->my_status !!}</td>
                                    @if(hasPermission('role_update') == true || hasPermission('role_delete') == true )
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"> 
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if(hasPermission('role_update') == true )
                                                    <a href="{{route('roles.edit',$role->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                @endif
                                                @if(hasPermission('role_create') == true )
                                                    <a href="{{route('roles.clone',$role->id)}}" class="dropdown-item"><i class="fas fa-clone" aria-hidden="true"></i> {{ __('levels.clone') }}</a>
                                                @endif
                                                @if( hasPermission('role_delete') == true )
                                                    <form action="{{route('role.delete',$role->id)}}" method="POST" id="delete" data-title="{{ __('delete.role') }}">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit"  class="dropdown-item"><i class="fa fa-trash" aria-hidden="true"></i> {{ __('levels.delete') }}</button>
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
                    <span>{{ $roles->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $roles->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $roles->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $roles->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection()

