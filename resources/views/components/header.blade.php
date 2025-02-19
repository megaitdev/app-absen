<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <ul class="navbar-nav mr-auto">
        <li>
            <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>
    <ul class="navbar-nav navbar-righ ml-5">
        <li class="dropdown">
            @auth
                <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <img alt="image" src="{{ asset('img/profile/' . auth()->user()->picture) }}"
                        class="rounded-circle mr-1">
                    <div class="d-sm-none d-lg-inline-block">
                        {{ auth()->check() ? substr(auth()->user()->nama, 0, 20) : 'Guest' }}
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">
                        Selamat Datang, {{ auth()->check() ? substr(auth()->user()->nama, 0, 20) : 'Guest' }}
                    </div>
                    <a class="dropdown-item has-icon edit-profile" href="{{ url('profile') }}">
                        <i class="fa fa-user"></i> Edit Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ url('logout') }}" class="dropdown-item has-icon text-danger"
                        onclick="event.preventDefault(); localStorage.clear();  document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
                        {{ csrf_field() }}
                    </form>
                </div>
            @endauth
        </li>
    </ul>
</nav>
