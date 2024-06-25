@extends('layouts.dashboard')

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Profile') }}</h1>

    @if (session('success'))
        <div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-left-danger" role="alert">
            <ul class="pl-4 my-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">

        <div class="col-lg-8 order-lg-1">

            <div class="card shadow mb-445">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Account</h6>
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('profile.update') }}" autocomplete="off">
                        @csrf
                        @method('PUT')

                        <h6 class="heading-small text-muted mb-4">User information</h6>

                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group focused">
                                        <label class="form-control-label" for="name">Name<span class="small text-danger">*</span></label>
                                        <input type="text" id="name" class="form-control" name="name" placeholder="Name" value="{{ old('name', Auth::user()->name) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group focused">
                                        <label class="form-control-label" for="phone">Phone</label>
                                        <input type="text" id="phone" class="form-control" name="phone" placeholder="06 00 00 00 00" value="{{ old('phone', Auth::user()->phone) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="email">Email address<span class="small text-danger">*</span></label>
                                        <input type="email" id="email" class="form-control" name="email" placeholder="example@example.com" value="{{ old('email', Auth::user()->email) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="address">Address</label>
                                        <input type="text" id="address" class="form-control" name="address" placeholder="123 Main St" value="{{ old('address', Auth::user()->address) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group focused">
                                        <label class="form-control-label" for="current_password">Current password</label>
                                        <input type="password" id="current_password" class="form-control" name="current_password" placeholder="Current password">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group focused">
                                        <label class="form-control-label" for="new_password">New password</label>
                                        <input type="password" id="new_password" class="form-control" name="new_password" placeholder="New password">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group focused">
                                        <label class="form-control-label" for="confirm_password">Confirm password</label>
                                        <input type="password" id="confirm_password" class="form-control" name="password_confirmation" placeholder="Confirm password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Button -->
                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col text-center">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Account Section -->
                    <hr class="my-4" />
                    <h6 class="heading-small text-muted mb-4">Delete Account</h6>
                    <p class="text-danger">
                        Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
                    </p>
                    <form method="POST" action="{{ route('profile.destroy') }}" id="delete-account-form">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="password" id="password" value="">

                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col text-center">
                                    <button type="button" class="btn btn-danger" id="delete-account-btn">Delete Account</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>

            </div>

        </div>

    </div>

    <!-- Include SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert2 Script -->
    <script>
    document.getElementById('delete-account-btn').addEventListener('click', function (e) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure you want to delete your account?',
            inputLabel: 'Enter your password to confirm',
            input: 'password',
            inputPlaceholder: 'Enter your password',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Delete',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Password is required');
                }
                return password;
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('password').value = result.value;
                document.getElementById('delete-account-form').submit();
            }
        });
    });
    </script>
@endsection
