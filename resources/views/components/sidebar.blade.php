@auth
    @switch(Auth::user()->role)
        @case('admin')
            @include('components.admin.sidebar')
        @break

        @case('pic')
            @include('components.pic.sidebar')
        @break
    @endswitch

@endauth
