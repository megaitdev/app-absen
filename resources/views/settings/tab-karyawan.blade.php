<div class="row">
    <div class="col-lg-12">
        <div class="card card-dark">
            <div class="card-header">
                <h4>List Karyawan</h4>
                <div class="card-header-action">
                    <div class="d-flex justify-content-end">
                        <a href="{{ url('settings/shift/tambah') }}" class="btn btn-outline-dark pt-1 mx-1">
                            <i class="fa fa-plus mr-1"></i> Tambah
                        </a>
                        <div class="search-container mx-1">
                            <input type="text" class="search-input-dark" id="search-shift" placeholder="Search">
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
                    <table class="table table-sm table-light table-bordered table-karyawan">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th>#</th>
                                <th>NIK</th>
                                <th>Nama Karyawan</th>
                                <th>PIN</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
