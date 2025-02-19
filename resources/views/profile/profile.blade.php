@extends('layouts.app')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $title }}</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if (session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                {{ session('warning') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if (session('danger'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('danger') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-dark">
                            <div class="card-body">
                                <ul class="nav nav-pills" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link nav-link-dark {{ $profile_tab == 'profile' ? 'active' : '' }}"
                                            id="profile-tab" data-toggle="tab" href="#profile">
                                            Profile
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link nav-link-dark {{ $profile_tab == 'change-password' ? 'active' : '' }}"
                                            id="change-password-tab" data-toggle="tab" href="#change-password">
                                            Change Password
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade {{ $profile_tab == 'profile' ? 'show active' : '' }}"
                                        id="profile" role="tabpanel">
                                        @include('profile.tab-profile')
                                    </div>
                                    <div class="tab-pane fade {{ $profile_tab == 'change-password' ? 'show active' : '' }}"
                                        id="change-password" role="tabpanel">
                                        @include('profile.tab-change-password')

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
