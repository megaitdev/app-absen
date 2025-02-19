<div class="row">
    <div class="col-lg-12">
        <div class="card card-dark">
            <div class="card-header">
                <h4>List Schedule</h4>
                <div class="card-header-action">
                    <div class="d-flex justify-content-end">
                        <a href="{{ url('settings/schedule/tambah') }}" class="btn btn-outline-dark pt-1 mx-1">
                            <i class="fa fa-plus mr-1"></i> Tambah
                        </a>
                        <div class="search-container mx-1">
                            <input type="text" class="search-input-dark" id="search-schedule" placeholder="Search">
                            <button class="search-button">
                                <svg class="search-icon-dark" viewBox="0 0 24 24">
                                    <path
                                        d="M21.71 20.29l-5.01-5.01C17.54 13.68 18 11.91 18 10c0-4.41-3.59-8-8-8S2 5.59 2 10s3.59 8 8 8c1.91 0 3.68-0.46 5.28-1.3l5.01 5.01c0.39 0.39 1.02 0.39 1.41 0C22.1 21.32 22.1 20.68 21.71 20.29zM10 16c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6S13.31 16 10 16z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-light table-bordered  table-schedule">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th>#</th>
                                <th>Schedule</th>
                                <th>Note</th>
                                <th>Senin</th>
                                <th>Selasa</th>
                                <th>Rabu</th>
                                <th>Kamis</th>
                                <th>Jumat</th>
                                <th>Sabtu</th>
                                <th>Minggu</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>


                {{-- Modal Edit Schedule --}}
                <div class="modal fade" id="modal-edit-schedule">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4>Edit Schedule</h4>
                            </div>
                            <div class="modal-body">
                                <form action="{{ url('settings/schedule/edit') }}" method="post">
                                    @csrf
                                    <input type="text" id="edit-id-schedule" name="shcedule_id" hidden>
                                    <div class="form-group">
                                        <label for="edit-schedule">Nama Schedule</label>
                                        <input type="text"
                                            class="form-control @error('schedule') is-invalid @enderror"
                                            id="edit-schedule" name="schedule" value="{{ old('schedule') }}">
                                        @error('schedule')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="edit-keterangan-schedule">Keterangan</label>
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror h-100" id="edit-keterangan" name="keterangan"
                                            rows="3">{{ old('keterangan') }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Hari</th>
                                                <th>Shift</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                                                <tr>
                                                    <td class="text-center">{{ $day }}</td>
                                                    <td>
                                                        <select
                                                            class="form-control select2 @error(strtolower($day)) is-invalid @enderror"
                                                            name="{{ strtolower($day) }}" id="{{ strtolower($day) }}">
                                                            <option value="libur">Libur</option>
                                                        </select>
                                                        @error(strtolower($day))
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-dark float-right">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Show Shift --}}
                <div class="modal fade" id="modal-show-shift">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="text-dark"> Detail Shift</h4>
                                <i class="fas fa-calendar mr-1 float-end text-dark" style="font-size: 1.5em"></i>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name">Nama Shift</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="show-name-shift" name="name" value="{{ old('name') }}" disabled>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="show-jam_masuk-shift">Jam Masuk</label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('jam_masuk') is-invalid @enderror timepicker"
                                                id="show-jam_masuk-shift" name="jam_masuk"
                                                value="{{ old('jam_masuk') }}" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                                            </div>
                                            @error('jam_masuk')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="show-jam_keluar-shift">Jam Keluar</label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('jam_keluar') is-invalid @enderror timepicker"
                                                id="show-jam_keluar-shift" name="jam_keluar"
                                                value="{{ old('jam_keluar') }}" disabled>
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
                                        <input id="show-is_sameday-shift" type="checkbox" name="is_sameday"
                                            class="custom-switch-input" disabled>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Shift Sameday</span>
                                    </label>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="show-jam_mulai_istirahat-shift">Jam Masuk Istirahat</label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('jam_mulai_istirahat') is-invalid @enderror timepicker"
                                                id="show-jam_mulai_istirahat-shift" name="jam_mulai_istirahat"
                                                value="{{ old('jam_mulai_istirahat') }}" disabled>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                                            </div>
                                            @error('jam_mulai_istirahat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="show-jam_selesai_istirahat-shift">Jam Keluar Istirahat</label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('jam_selesai_istirahat') is-invalid @enderror timepicker"
                                                id="show-jam_selesai_istirahat-shift" name="jam_selesai_istirahat"
                                                value="{{ old('jam_selesai_istirahat') }}" disabled>
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
                                        <input id="show-is_break-shift" type="checkbox" name="is_break"
                                            class="custom-switch-input" disabled>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description" data-toggle="tooltip"
                                            data-placement="top"
                                            title="Jam Istirahat aktif maka shift memiliki jam istirahat">Jam
                                            Istirahat</span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="show-keterangan-shift">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror h-100" id="show-keterangan-shift"
                                        name="keterangan" rows="3" disabled>{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-light float-right"
                                        data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="session">
        @if (session('success-schedule'))
            <input type="text" id="alert-success-schedule" value="1" hidden></input>
        @endif
        @if (session('updated-schedule'))
            <input type="text" id="alert-updated-schedule" value="1" hidden></input>
        @endif
        @if ($errors->any(['schedule']))
            <input type="text" id="alert-modal-schedule" value="{{ old('shcedule_id') }}" hidden></input>
        @endif
    </div>
</div>
