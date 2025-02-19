    <form method="post" action="{{ url('profile/change-password') }}" class="needs-validation" novalidate="">
        @csrf
        <div class="row">
            <div class="form-group col-md-6 col-12">
                <label>New Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                    required>
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="form-group col-md-6 col-12">
                <label>Confirm Password</label>
                <input type="password" class="form-control" name="password_confirmation" required>
            </div>
        </div>
        <div class="card-footer text-right">
            <button class="btn btn-primary">Save Changes</button>
        </div>
    </form>
