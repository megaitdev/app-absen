<div class="row">
    <div class="col-lg-8">
        <div class="card card-dark">
            <div class="card-header">
                <h4>List Holiday</h4>
                <div class="card-header-action">
                    <div class="d-flex justify-content-end">
                        <a href="{{ url('settings/holidays/tambah') }}" class="btn btn-outline-dark pt-1 mx-1">
                            <i class="fa fa-plus mr-1"></i> Tambah
                        </a>
                        <div class="btn btn-outline-dark pt-1 mx-1 yearpicker">
                            <i class="far fa-calendar mr-1"></i> 2024
                        </div>
                        <div class="search-container mx-1">
                            <input type="text" class="search-input-dark" id="search-holidays" placeholder="Search">
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
                    <table class="table table-sm table-light table-bordered  table-holidays">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th>#</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Holiday Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="modal fade" id="modal-edit-holiday">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4>Edit Holiday</h4>
                            </div>
                            <div class="modal-body">
                                <form action="{{ url('settings/holidays/edit') }}" method="post">
                                    @csrf
                                    <input type="text" id="edit-id-holiday" name="holiday_id" hidden>
                                    <div class="form-group">
                                        <label for="date">Tanggal</label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror"
                                            id="edit-date-holiday" name="date" value="{{ old('date') }}"
                                            autocomplete="off">
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="note">Keterangan</label>
                                        <input type="text" class="form-control @error('note') is-invalid @enderror"
                                            id="edit-note-holiday" name="note" value="{{ old('note') }}">
                                        @error('note')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-dark float-right">Simpan</button>
                                    </div>
                                </form>
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
        @if (session('success-holiday'))
            <input type="text" id="alert-success-holiday" value="1" hidden></input>
        @endif
        @if (session('updated-holiday'))
            <input type="text" id="alert-updated-holiday" value="1" hidden></input>
        @endif
        @if ($errors->any(['note', 'date']))
            <input type="text" id="alert-modal-holiday" value="{{ old('holiday_id') }}" hidden></input>
        @endif
    </div>
</div>
