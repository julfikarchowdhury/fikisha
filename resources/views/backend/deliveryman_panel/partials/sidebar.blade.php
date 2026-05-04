<!-- left sidebar -->
<div class="col-12 nav-left-sidebar sidebar-dark">
    <ul class="navbar-nav">
        <li class="nav-divider">
            {{ __('menus.menu') }}
        </li>
        <li class="nav-item "> 
            <a class="nav-link {{ (request()->is('dashboard*')) ? 'active' : '' }}" href="{{url('/dashboard')}}" aria-expanded="false" data-target="#submenu-1" aria-controls="submenu-1"><i class="fa fa-home"></i>{{__('menus.dashboard') }}</a>
        </li>
    </ul>
</div>
<!-- end left sidebar -->

