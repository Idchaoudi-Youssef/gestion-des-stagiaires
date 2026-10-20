@extends('layouts.auth')

@section('main-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block">
                            <img src="{{ asset('13.jpg') }}" alt="" style="width: 400px; margin-left: 60px">
                        </div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h1 text-gray-900 mb-4">{{ __('Reset your password') }}</h1>
                                </div>

                                <p class="text-center text-muted small">
                                    {{ __('Enter your email address and the new password you want to use.') }}
                                </p>

                                @if ($errors->any())
                                    <div class="alert alert-danger border-left-danger" role="alert">
                                        <ul class="pl-4 my-2">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('password.store') }}" class="user">
                                    @csrf

                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                    <div class="form-group">
                                        <label for="email">{{ __('E-Mail Address') }}</label>
                                        <input type="email" id="email" class="form-control" name="email"
                                               value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                                    </div>

                                    <div class="form-group">
                                        <label for="password">{{ __('Password') }}</label>
                                        <input type="password" id="password" class="form-control" name="password"
                                               required autocomplete="new-password">
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                                        <input type="password" id="password_confirmation" class="form-control"
                                               name="password_confirmation" required autocomplete="new-password">
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            {{ __('Reset Password') }}
                                        </button>
                                    </div>
                                </form>

                                <div class="text-center mt-3">
                                    <a href="{{ route('login') }}">{{ __('Back to the login page') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
