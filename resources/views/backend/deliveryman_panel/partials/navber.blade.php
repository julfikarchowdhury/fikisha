
        <!-- navbar -->
        <nav class="navbar navbar-expand-lg center-nav transparent navbar-light p-3 fixed-top">
            <div class="container flex-lg-row flex-nowrap align-items-center">
                <div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start text-bg-dark " tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
                    <div class="offcanvas-header w-90 ">
                        <h3 class="text-white fs-30 mb-0">{{ settings()->name }}</h3>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body ms-lg-auto d-flex flex-column h-100 w-90">
                        <div class="dashboard-header">
                            <nav class="navbar navbar-expand-lg navbar-light  fixed-top   " >
                                <a class="navbar-brand" href="{{url('/')}}">
                                    <img src="{{ settings()->logo_image }}" class="logo"/>
                                </a>
                                <div class="dropdown lang-dropdown navbar_menus changeLocale mobileLocale ">
                                    <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        @if(app()->getLocale() == "en")
                                            <i class="flag-icon flag-icon-us"></i> {{ __('levels.english') }}
                                        @elseif(app()->getLocale() == 'ar')
                                            <i class="flag-icon flag-icon-sa"></i> {{ __('levels.arabic') }}
                                        @elseif(app()->getLocale() == 'fr')
                                            <i class="flag-icon flag-icon-fr"></i> {{ __('levels.franch') }}
                                        @endif
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="{{ route('setlocalization','en') }}"> <i class="flag-icon flag-icon-us"></i> {{ __('levels.english') }}</a>
                                        <a class="dropdown-item" href="{{ route('setlocalization','ar') }}"> <i class="flag-icon flag-icon-sa"></i> {{ __('levels.arabic') }}</a>
                                        <a class="dropdown-item" href="{{ route('setlocalization','fr') }}"> <i class="flag-icon flag-icon-fr"></i> {{ __('levels.franch') }}</a>
                                       
                                    </div>
                                    </div>
                                <div class=" navbar-collapse  " id="navbarSupportedContent">
                                    <ul class="navbar-nav ml-auto navbar-right-top">
                                        <li class="nav-item lang">
                                            <div class="form-group col-12 pt-1">
                                                <div class="dropdown lang-dropdown">
                                                    <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        @if(app()->getLocale() == "en")
                                                            <i class="flag-icon flag-icon-us"></i> {{ __('levels.english') }}
                                                        @elseif(app()->getLocale() == 'ar')
                                                            <i class="flag-icon flag-icon-sa"></i> {{ __('levels.arabic') }}
                                                        @elseif(app()->getLocale() == 'fr')
                                                            <i class="flag-icon flag-icon-fr"></i> {{ __('levels.franch') }}
                                                        @endif
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{ route('setlocalization','en') }}"> <i class="flag-icon flag-icon-us"></i> {{ __('levels.english') }}</a>
                                                        <a class="dropdown-item" href="{{ route('setlocalization','ar') }}"> <i class="flag-icon flag-icon-sa"></i> {{ __('levels.arabic') }}</a>
                                                        <a class="dropdown-item" href="{{ route('setlocalization','fr') }}"> <i class="flag-icon flag-icon-fr"></i> {{ __('levels.franch') }}</a>
                                                        
                                                    </div>
                                                    </div>
                                            </div>
                                        </li>
                                        <li class="nav-item dropdown nav-user navbar_menus"> 
                                            <a class="dropdown-item {{ (request()->is('/*')) ? 'active' : '' }}" href="{{url('/')}}" aria-expanded="false" data-target="#submenu-1" aria-controls="submenu-1"><i class="fa fa-home"></i> {{__('menus.dashboard') }}</a>
                                        </li>
                                 
                                        <li class="nav-item dropdown admin-panel notification  d-lg-block">
                                            <a href="{{ url('/') }}" class="me-2"><i class="fa fa-globe navbar-globe"></i></a>
                                        </li>
                                        
                                        <li class="nav-item dropdown admin-panel notification d-lg-block">
                                            <a class="nav-link nav-icons mt-md-3" href="#" id="navbarDropdownMenuLink1" data-toggle="dropdown"   aria-haspopup="true" aria-expanded="false"><i class="fas fa-fw fa-bell"></i> <span class="indicator"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-right notification-dropdown">
                                                <li>
                                                    <div class="notification-title"> Notification</div>
                                                    <div class="notification-list">
                                                        <div class="list-group">
                                                            @foreach (notifications() as $notify )
                                                                <a href="
                                                                @if($notify['type'] === 'support') {{ route('support.view',$notify['support_id']) }}
                                                                @elseif($notify['type'] === 'newsoffer') {{ route('news-offer.index') }} @endif"
                                                                class="list-group-item list-group-item-action active">
                                                                    <div class="notification-info">
                                                                        <div class="notification-list-user-img">
                                                                            <img src="{{ singleUser($notify['user_id'])->image }}" class="user-avatar-md rounded-circle">
                                                                        </div>
                                                                        <div class="notification-list-user-block">
                                                                            <span class="notification-list-user-name">
                                                                                {{ singleUser($notify['user_id'])->name }}
                                                                            </span>
                                                                            {{ $notify['subject'] }}
                                                                            <div class="notification-date">
                                                                                {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notify['created_at'])->diffForHumans() }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
                                        <!---To-do list---->
                                        @if(hasPermission('todo_create')== true)
                                        <li class="nav-item dropdown connection mt-xl-2 mt-md-2 mt-lg-2 d-lg-block">
                                            <label id="todoModal1" data-target="#todoModal" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-url="{{route('todo.modal')}}"><i class="fa fa-edit"></i> {{ __('to_do.to_do')}}</label>
                                        </li>
                                        @endif
                                        <!---To-do list---->
                                        <li class="nav-item dropdown nav-user d-lg-block">
                                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <img src="{{Auth::user()->image}}" alt="" class="user-avatar-md rounded-circle" style="object-fit: contain">
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                                <div class="nav-user-info">
                                                    <h5 class="mb-0 text-white nav-user-name">{{ Auth::user()->name }}</h5>
                                                </div>
                                                <a class="dropdown-item" href="{{route('profile.index')}}"><i class="fas fa-user mr-2"></i>{{ __('menus.profile') }}</a>
                                                <a class="dropdown-item" href="{{route('password.change')}}"><i class="fas fa-key mr-2"></i>{{ __('menus.change_password') }}</a>
                                                <a class="dropdown-item" href="{{ route('logout') }}"
                                                    onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                                    <i class="fas fa-power-off mr-2"></i>
                                                    {{ __('menus.logout') }}
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                    @csrf
                                                </form>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="navbar-other w-100 d-flex justify-content-between ">
                    <div  class="d-lg-none">
                        <a href="{{ url('/') }}">
                            <img src="{{ settings()->logo_image }}"  style="margin-top: 10px" width="150" alt="Logo">
                        </a>
                    </div>
                    <ul class="navbar-nav flex-row align-items-center ">
                        <li class="nav-item dropdown admin-panel notification  d-lg-none">
                            <a href="{{ url('/') }}" class="me-2"><i class="fa fa-globe"></i></a>
                        </li>
                        <li class="nav-item dropdown admin-panel notification  d-lg-none">
                            <a class="nav-link nav-icons mt-md-3" href="#" id="navbarDropdownMenuLink1" data-toggle="dropdown"   aria-haspopup="true" aria-expanded="false"><i class="fas fa-fw fa-bell"></i> <span class="mobile-notification indicator admin"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right notification-dropdown">
                                <li>
                                    <div class="notification-title"> Notification</div>
                                    <div class="notification-list">
                                        <div class="list-group">
                                            @foreach (notifications() as $notify )
                                                <a href="
                                                @if($notify['type'] === 'support') {{ route('support.view',$notify['support_id']) }}
                                                @elseif($notify['type'] === 'newsoffer') {{ route('news-offer.index') }} @endif"
                                                class="list-group-item list-group-item-action active">
                                                    <div class="notification-info">
                                                        <div class="notification-list-user-img">
                                                            <img src="{{ singleUser($notify['user_id'])->image }}" class="user-avatar-md rounded-circle">
                                                        </div>
                                                        <div class="notification-list-user-block">
                                                            <span class="notification-list-user-name">
                                                                {{ singleUser($notify['user_id'])->name }}
                                                            </span>
                                                            {{ $notify['subject'] }}
                                                            <div class="notification-date">
                                                                {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notify['created_at'])->diffForHumans() }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!---To-do list---->
                        <li class="nav-item dropdown nav-user mobile d-lg-none">
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{Auth::user()->image}}" alt="" class="user-avatar-md rounded-circle">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                <div class="nav-user-info">
                                    <h5 class="mb-0 text-white nav-user-name">{{ Auth::user()->name }}</h5>
                                </div>
                                <a class="dropdown-item" href="{{ route('profile.index') }}"><i class="fas fa-user mr-2"></i>{{ __('menus.profile') }}</a>
                                <a class="dropdown-item" href="{{ route('password.change') }}"><i class="fas fa-key mr-2"></i>{{ __('menus.change_password') }}</a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    <i class="fas fa-power-off mr-2"></i>
                                    {{ __('menus.logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        <li class="nav-item d-lg-none">
                            <button class="offcanvas-nav-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar"><span class="navbar-toggler-icon"></span></button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav> 