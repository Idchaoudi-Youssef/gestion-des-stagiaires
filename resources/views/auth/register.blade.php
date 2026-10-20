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
                                    <h1 class="h1 text-gray-900 mb-4">{{ __('Create your account') }}</h1>
                                </div>

                                <p class="text-center text-muted small">
                                    {{ __('Your account will be reviewed by an administrator before you get access to the platform.') }}
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

                                <form method="POST" action="{{ route('register') }}" class="user">
                                    @csrf

                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}</label>
                                        <input type="text" id="name" class="form-control" name="name"
                                               value="{{ old('name') }}" required autofocus autocomplete="name">
                                    </div>

                                    <div class="form-group">
                                        <label for="email">{{ __('E-Mail Address') }}</label>
                                        <input type="email" id="email" class="form-control" name="email"
                                               value="{{ old('email') }}" required autocomplete="username">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">{{ __('Phone') }}</label>
                                        <input type="text" id="phone" class="form-control" name="phone"
                                               value="{{ old('phone') }}" required autocomplete="tel">
                                    </div>

                                    <div class="form-group">
                                        <label for="address">{{ __('Address') }}</label>
                                        <input type="text" id="address" class="form-control" name="address"
                                               value="{{ old('address') }}" required autocomplete="street-address">
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
                                            {{ __('Register') }}
                                        </button>
                                    </div>
                                </form>

                                <div class="text-center mt-3">
                                    <a href="{{ route('login') }}">{{ __('Already registered?') }}</a>
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
