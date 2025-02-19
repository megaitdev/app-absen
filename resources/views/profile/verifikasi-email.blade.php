@extends('layouts.app')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ url()->previous() }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
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
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4 class="text-dark">Enter Verification Code</h4>
                            </div>
                            <div class="card-body ">
                                <div class="login-brand">
                                    <img src="{{ asset('gif/sent.gif') }}" alt="logo" width="75">
                                </div>
                                <p class="text-muted text-center">We've sent a code to
                                    <strong class="text-dark">{{ $user->email }}</strong>
                                </p>
                                <div class="form-group ">
                                    <label for="code">Verification Code</label>
                                    <input id="code" type="text" class="form-control" name="code" tabindex="1"
                                        required="" maxlength="6" style="text-transform:uppercase">
                                    <small id="error-code" class="form-text text-danger mb-0" hidden>Invalid
                                        code</small>
                                </div>
                                <div class="form-group mb-2">
                                    <button onclick="javascript:verifikasi()" class="btn btn-dark btn-lg btn-block">
                                        Submit
                                    </button>
                                    <input type="text" value="{{ $user }}" id="user" hidden>
                                    <div class="mt-5 text-muted text-center">
                                        Didn't get a code? <a href="javascript:resendVerificationCode()">Click to
                                            resend.</a>
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
