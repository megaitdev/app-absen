<div class="row">
    <div class="col-lg-8">
        <div class="card card-dark">
            <div class="card-header">
                <h4>Report Unit</h4>
                <div class="card-header-action">
                    <div class="d-flex justify-content-end">
                        <div class="dropdown mx-1">
                            <a href="javascript:void(0)" data-toggle="dropdown"
                                class="btn btn-outline-dark dropdown-toggle length-info-unit">
                                <i class="fas fa-layer-group mr-2"></i>10
                            </a>
                            <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                <li class="dropdown-title">Select Length</li>
                                <li>
                                    <a href="javascript:changeLengthUnit(5)"
                                        class="dropdown-item dropdown-item-dark diu diu-5">5</a>
                                </li>
                                <li>
                                    <a href="javascript:changeLengthUnit(10)"
                                        class="dropdown-item dropdown-item-dark diu diu-10 active">10</a>
                                </li>
                                <li>
                                    <a href="javascript:changeLengthUnit(25)"
                                        class="dropdown-item dropdown-item-dark diu diu-25">25</a>
                                </li>
                                <li>
                                    <a href="javascript:changeLengthUnit(50)"
                                        class="dropdown-item dropdown-item-dark diu diu-50">50</a>
                                </li>
                                <li>
                                    <a href="javascript:changeLengthUnit(100)"
                                        class="dropdown-item dropdown-item-dark diu diu-100">100</a>
                                </li>
                            </ul>
                        </div>
                        <div class="search-container mx-1">
                            <input type="text" class="search-input-dark" id="search-unit" placeholder="Search">
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
                <p>
                    <svg class="icon" viewBox="0 0 24 24" width="20" height="20" fill="#000">
                        <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z" />
                    </svg>
                    <svg class="icon trend-down" viewBox="0 0 24 24" width="20" fill="#000">
                        <path d="M16 18l2.29-2.29-4.88-4.88-4 4L2 7.41 3.41 6l6 6 4-4 6.3 6.29L22 12v6z" />
                    </svg>
                </p>
                <div class="table-responsive">
                    <table class="table table-sm table-light table-bordered table-unit">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th>#</th>
                                <th>Unit</th>
                                <th>Jumlah Karyawan</th>
                                <th>Performa Kehadiran </th>
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
