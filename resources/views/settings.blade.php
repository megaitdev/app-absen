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
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="nav nav-pills mb-2" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link nav-link-dark {{ $settings_tab == 'shift' ? 'active' : '' }}"
                                    id="shift-tab" data-toggle="tab" href="#shift">Shift</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-dark {{ $settings_tab == 'holidays' ? 'active' : '' }}"
                                    id="holidays-tab" data-toggle="tab" href="#holidays">Holidays</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-dark {{ $settings_tab == 'schedule' ? 'active' : '' }}"
                                    id="schedule-tab" data-toggle="tab" href="#schedule">Schedule</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-dark {{ $settings_tab == 'pic' ? 'active' : '' }}"
                                    id="pic-tab" data-toggle="tab" href="#pic">PIC</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-dark {{ $settings_tab == 'dasar-jadwal' ? 'active' : '' }}"
                                    id="dasar-jadwal-tab" data-toggle="tab" href="#dasar-jadwal">Dasar Jadwal</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade {{ $settings_tab == 'shift' ? 'show active' : '' }}" id="shift"
                                role="tabpanel">
                                @include('settings.tab-shift')
                            </div>
                            <div class="tab-pane fade {{ $settings_tab == 'holidays' ? 'show active' : '' }}" id="holidays"
                                role="tabpanel">
                                @include('settings.tab-holidays')
                            </div>
                            <div class="tab-pane fade {{ $settings_tab == 'schedule' ? 'show active' : '' }}" id="schedule"
                                role="tabpanel">
                                @include('settings.tab-schedule')
                            </div>
                            <div class="tab-pane fade {{ $settings_tab == 'pic' ? 'show active' : '' }}" id="pic"
                                role="tabpanel">
                                @include('settings.tab-pic')
                            </div>
                            <div class="tab-pane fade {{ $settings_tab == 'dasar-jadwal' ? 'show active' : '' }}"
                                id="dasar-jadwal" role="tabpanel">
                                @include('settings.tab-dasar-jadwal')
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
