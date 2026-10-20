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
                                    <h1 class="h1 text-gray-900 mb-4">{{ __('Forgot your password?') }}</h1>
                                </div>

                                <p class="text-center text-muted small">
                                    {{ __('Enter your email address and we will send you a link to choose a new password.') }}
                                </p>

                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">{{ session('status') }}</div>
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

                                <form method="POST" action="{{ route('password.email') }}" class="user">
                                    @csrf

                                    <div class="form-group">
                                        <label for="email">{{ __('E-Mail Address') }}</label>
                                        <input type="email" id="email" class="form-control" name="email"
                                               value="{{ old('email') }}" required autofocus>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            {{ __('Send the reset link') }}
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
