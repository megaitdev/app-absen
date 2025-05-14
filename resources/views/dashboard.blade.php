@extends('layouts.app')

@section('title', 'Blank Page')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $title }}</h1>
            </div>

            <div class="section-body">
                <h2 class="section-title">Dashboard Admin</h2>
                <p class="section-lead">
                    This page is an example of using the blank page template.
                    This template is suitable for making the landing page, login page, and others.
                </p>

                <div class="row">
                    <div class="col-lg-5">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Card Header</h4>
                            </div>
                            <div class="card-body">
                                <p>Card <code>.card-primary</code></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
@endpush
