@extends('layouts.app')

@section('title', 'Blank Page')

@push('style')
    <!-- CSS Libraries -->
    <style>
        /* Fully block interaction on disabled extra break rows (including timepicker icons) */
        .extra-break-row.is-disabled {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
@endpush

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ url()->previous() }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ $title }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('settings') }}">Settings</a></div>
                    <div class="breadcrumb-item">{{ $title }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4>Formulir Tambah Shift</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ url('settings/shift/store') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Nama Shift</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="jam_masuk">Jam Masuk</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('jam_masuk') is-invalid @enderror timepicker"
                                                    id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                </div>
                                                @error('jam_masuk')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="jam_keluar">Jam Keluar</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('jam_keluar') is-invalid @enderror timepicker"
                                                    id="jam_keluar" name="jam_keluar" value="{{ old('jam_keluar') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                </div>
                                                @error('jam_keluar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="custom-switch p-0">
                                            <input type="checkbox" name="is_sameday" id="is_sameday"
                                                class="custom-switch-input active" checked>
                                            <span class="custom-switch-indicator"></span>
                                            <span class="custom-switch-description" data-toggle="tooltip"
                                                data-placement="top"
                                                title="Shift Sameday adalah shift dimana berjalan pada hari yang sama atau tidak melebihi jam 12 malam">
                                                Shift Sameday
                                            </span>
                                        </label>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="jam_mulai_istirahat">Jam Masuk Istirahat</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('jam_mulai_istirahat') is-invalid @enderror timepicker"
                                                    id="jam_mulai_istirahat" name="jam_mulai_istirahat"
                                                    value="{{ old('jam_mulai_istirahat') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                </div>
                                                @error('jam_mulai_istirahat')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="jam_selesai_istirahat">Jam Keluar Istirahat</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('jam_selesai_istirahat') is-invalid @enderror timepicker"
                                                    id="jam_selesai_istirahat" name="jam_selesai_istirahat"
                                                    value="{{ old('jam_selesai_istirahat') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                </div>
                                                @error('jam_selesai_istirahat')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="custom-switch p-0">
                                            <input type="checkbox" name="is_break" class="custom-switch-input active"
                                                id="is_break" onclick="handleIsBreak()"
                                                {{ old('is_break', 1) ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                            <span class="custom-switch-description" data-toggle="tooltip"
                                                data-placement="top"
                                                title="Jam Istirahat aktif maka shift memiliki jam istirahat">Jam
                                                Istirahat</span>
                                        </label>
                                    </div>

                                    <div class="form-group" id="extraBreakToggleWrapper"
                                        style="{{ old('is_break', 1) ? '' : 'display:none;' }}">
                                        <label class="custom-switch p-0">
                                            <input type="checkbox" name="is_break_extra" class="custom-switch-input"
                                                id="is_break_extra" onclick="handleIsBreakExtra()"
                                                {{ old('is_break_extra') ? 'checked' : '' }}>
                                            <span class="custom-switch-indicator"></span>
                                            <span class="custom-switch-description" data-toggle="tooltip"
                                                data-placement="top"
                                                title="Aktifkan jika ada jam istirahat tambahan (kedua)">Jam Istirahat
                                                Tambahan</span>
                                        </label>
                                    </div>

                                    <div id="extraBreakFields"
                                        style="{{ old('is_break_extra') ? '' : 'display:none;' }}">
                                        <div id="extraBreakContainer">
                                            @php
                                                $oldMulaiExtras = old('jam_mulai_istirahat_extra', []);
                                                $oldSelesaiExtras = old('jam_selesai_istirahat_extra', []);
                                                $rowCount = max(count($oldMulaiExtras), count($oldSelesaiExtras));
                                            @endphp
                                            @for ($i = 0; $i < $rowCount; $i++)
                                                <div class="form-row extra-break-row">
                                                    <div class="form-group col-md-5">
                                                        <label>Jam Masuk Istirahat (Tambahan)</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control timepicker"
                                                                name="jam_mulai_istirahat_extra[]"
                                                                value="{{ $oldMulaiExtras[$i] ?? '' }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text"><i
                                                                        class="far fa-clock"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-5">
                                                        <label>Jam Keluar Istirahat (Tambahan)</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control timepicker"
                                                                name="jam_selesai_istirahat_extra[]"
                                                                value="{{ $oldSelesaiExtras[$i] ?? '' }}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text"><i
                                                                        class="far fa-clock"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-2 d-flex align-items-end">
                                                        <button type="button"
                                                            class="btn btn-outline-danger w-100 remove-extra-break">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-outline-dark" id="addExtraBreakBtn">
                                                <i class="fas fa-plus"></i> Tambah Istirahat Tambahan
                                            </button>
                                        </div>
                                    </div>

                                    <script>
                                        function setDisabled(el, disabled) {
                                            if (!el) return;
                                            el.disabled = !!disabled;
                                            el.readOnly = !!disabled;
                                            if (disabled) {
                                                el.classList.add('disabled');
                                                try {
                                                    el.blur && el.blur();
                                                } catch (e) {}
                                            } else {
                                                el.classList.remove('disabled');
                                            }
                                        }


                                        function handleIsBreak() {
                                            var isBreak = document.getElementById('is_break');
                                            var mulai = document.getElementById('jam_mulai_istirahat');
                                            var selesai = document.getElementById('jam_selesai_istirahat');
                                            var extraToggleWrapper = document.getElementById('extraBreakToggleWrapper');
                                            var extraFields = document.getElementById('extraBreakFields');
                                            var isBreakExtra = document.getElementById('is_break_extra');
                                            var extraContainer = document.getElementById('extraBreakContainer');
                                            var addBtn = document.getElementById('addExtraBreakBtn');

                                            var active = isBreak && isBreak.checked;
                                            setDisabled(mulai, !active);
                                            setDisabled(selesai, !active);

                                            if (extraToggleWrapper) {
                                                extraToggleWrapper.style.display = active ? '' : 'none';
                                            }
                                            if (!active) {
                                                // If main break disabled, also hide/disable extra
                                                if (isBreakExtra) {
                                                    isBreakExtra.checked = false;
                                                    isBreakExtra.disabled = true;
                                                }
                                                if (extraFields) extraFields.style.display = 'none';
                                                if (addBtn) addBtn.disabled = true;
                                                if (extraContainer) {
                                                    var inputs = extraContainer.querySelectorAll('input');
                                                    inputs.forEach(function(el) {
                                                        setDisabled(el, true);
                                                    });
                                                    extraContainer.querySelectorAll('.extra-break-row').forEach(function(r) {
                                                        r.classList.add('is-disabled');
                                                    });
                                                }
                                            } else {
                                                // main break enabled, defer extra state to its handler
                                                if (isBreakExtra) isBreakExtra.disabled = false;
                                                handleIsBreakExtra();
                                                if (addBtn) {
                                                    var activeExtra = isBreakExtra && isBreakExtra.checked;
                                                    addBtn.disabled = !activeExtra;
                                                }
                                            }
                                        }

                                        function handleIsBreakExtra() {
                                            var isBreakExtra = document.getElementById('is_break_extra');
                                            var extraFields = document.getElementById('extraBreakFields');
                                            var extraContainer = document.getElementById('extraBreakContainer');

                                            var active = isBreakExtra && isBreakExtra.checked;
                                            if (extraFields) extraFields.style.display = active ? '' : 'none';
                                            if (extraContainer) {
                                                var inputs = extraContainer.querySelectorAll('input');
                                                inputs.forEach(function(el) {
                                                    setDisabled(el, !active);
                                                });
                                                extraContainer.querySelectorAll('.extra-break-row').forEach(function(r) {
                                                    r.classList.toggle('is-disabled', !active);
                                                });
                                            }
                                        }

                                        document.addEventListener('DOMContentLoaded', function() {
                                            // Initialize states on load using current checkbox values
                                            var extraContainer = document.getElementById('extraBreakContainer');
                                            var addBtn = document.getElementById('addExtraBreakBtn');

                                            function initTimepickerFor(container) {
                                                try {
                                                    if (window.$ && $.fn && $.fn.timepicker) {
                                                        $(container).find('.timepicker').timepicker();
                                                    }
                                                } catch (e) {
                                                    /* ignore if plugin not present */
                                                }
                                            }

                                            function bindRowEvents(row) {
                                                var removeBtn = row.querySelector('.remove-extra-break');
                                                if (removeBtn) {
                                                    removeBtn.addEventListener('click', function() {
                                                        row.parentNode.removeChild(row);
                                                    });
                                                }
                                            }

                                            function createExtraRow(values) {
                                                var row = document.createElement('div');
                                                row.className = 'form-row extra-break-row';
                                                var mulaiVal = (values && values.mulai) ? values.mulai : '';
                                                var selesaiVal = (values && values.selesai) ? values.selesai : '';
                                                row.innerHTML = `
                                                    <div class="form-group col-md-5">
                                                        <label>Jam Masuk Istirahat (Tambahan)</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control timepicker" name="jam_mulai_istirahat_extra[]" value="${mulaiVal}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-5">
                                                        <label>Jam Keluar Istirahat (Tambahan)</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control timepicker" name="jam_selesai_istirahat_extra[]" value="${selesaiVal}">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-outline-danger w-100 remove-extra-break"><i class="fas fa-trash"></i></button>
                                                    </div>`;
                                                extraContainer.appendChild(row);
                                                initTimepickerFor(row);
                                                bindRowEvents(row);
                                                return row;
                                            }

                                            if (addBtn) {
                                                addBtn.addEventListener('click', function() {
                                                    var row = createExtraRow();
                                                    var isBreakExtra = document.getElementById('is_break_extra');
                                                    var active = isBreakExtra && isBreakExtra.checked;
                                                    // respect toggle state
                                                    var inputs = row.querySelectorAll('input');
                                                    inputs.forEach(function(el) {
                                                        setDisabled(el, !active);
                                                    });
                                                    row.classList.toggle('is-disabled', !active);
                                                });
                                            }

                                            // If is_break_extra was checked but there are no rows (no old values), add one by default
                                            var isBreakExtra = document.getElementById('is_break_extra');
                                            var hasRows = extraContainer && extraContainer.querySelector('.extra-break-row');
                                            if (isBreakExtra && isBreakExtra.checked && !hasRows) {
                                                createExtraRow();
                                            }

                                            // Init any server-rendered rows and timepicker
                                            if (extraContainer) {
                                                initTimepickerFor(extraContainer);
                                                extraContainer.querySelectorAll('.extra-break-row').forEach(function(r) {
                                                    bindRowEvents(r);
                                                });
                                            }

                                            // Wire change listeners to ensure toggles stay in sync
                                            var isBreakCb = document.getElementById('is_break');
                                            var isBreakExtraCb = document.getElementById('is_break_extra');
                                            if (isBreakCb) isBreakCb.addEventListener('change', handleIsBreak);
                                            if (isBreakExtraCb) isBreakExtraCb.addEventListener('change', function() {
                                                handleIsBreakExtra();
                                                if (addBtn) addBtn.disabled = !(isBreakExtraCb && isBreakExtraCb.checked);
                                            });

                                            handleIsBreak();
                                        });
                                    </script>
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror h-100" id="keterangan" name="keterangan"
                                            rows="3">{{ old('keterangan') }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-dark float-right">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-7">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h4>Upload List Liburan</h4>
                                <div class="card-header-action">
                                    <a href="" class="btn btn-outline-dark pt-1 mx-1">
                                        <i class="fas fa-file-download"></i> Template
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">


                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
@endpush
