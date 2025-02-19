    <form method="post" action="{{ url('profile/update') }}" class="needs-validation" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <ul class="list-unstyled list-unstyled-border list-unstyled-noborder mt-4 form-group">
                    <label>Photo Profile</label>
                    <li class="media">
                        <img alt="image" class="mr-3 rounded-circle" width="100"
                            src="{{ asset('img/profile/' . $user->picture) }}">
                        <div class="media-body">
                            <div class="media-title mb-0">Upload your photo</div>
                            <small class="mt-0"><i>Image should be at least 140px x 140px</i></small>
                            <input type="file" accept="image/*" class="form-control-file" name="picture">
                        </div>
                    </li>
                </ul>
            </div>
            <div class="form-group col-md-6 col-12">
                <label>Nama</label>
                <input type="text" class="form-control" value="{{ $user->nama }}" name="nama">
                <div class="invalid-feedback">
                    Please fill in name
                </div>
            </div>
            <div class="form-group col-md-6 col-12">
                <label>Username</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror"
                    value="{{ $user->username }}" name="username">
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6 col-12">
                @if ($user->email_verified_at)
                    <label>Email <i class="fas fa-check-circle text-success"></i></label>
                    <small class="text-success">Verified</small>
                @else
                    <label>Email <i class="fas fa-exclamation-circle text-warning"></i></label>
                    <small class="text-warning"> Not Verified</small>
                    <small class="float-right">
                        <a class="btn btn-sm btn-success" href="{{ url('profile/verifikasi-email/' . $user->id) }}"><i
                                class="fas fa-check-circle"></i>
                            Verification
                        </a>
                    </small>
                @endif
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ $user->email }}" name="email">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-md-6 col-12">
                @if ($user->nomor_wa_verified_at)
                    <label>Nomor WA <i class="fas fa-check-circle text-success"></i></label>
                    <small class="text-success">Verified</small>
                @else
                    <label>Nomor WA <i class="fas fa-exclamation-circle text-warning"></i></label>
                    <small class="text-warning"> Not Verified</small>
                    <small class="float-right">
                        <a class="btn btn-sm btn-success" href="{{ url('profile/verifikasi-nomor/' . $user->id) }}"><i
                                class="fas fa-check-circle"></i>
                            Verification
                        </a>
                    </small>
                @endif
                <input type="text" class="form-control @error('nomor_wa') is-invalid @enderror"
                    value="{{ $user->nomor_wa }}" name="nomor_wa">
                @error('nomor_wa')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="card-footer text-right px-0">
            <button class="btn btn-primary">Save Changes</button>
        </div>
    </form>
