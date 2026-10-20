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
                                    <h1 class="h1 text-gray-900 mb-4">{{ __('Verify your email address') }}</h1>
                                </div>

                                <p class="text-center text-muted small">
                                    {{ __('Thanks for signing up! Before getting started, please confirm your email address by clicking on the link we just sent you.') }}
                                </p>

                                @if (session('status') === 'verification-link-sent')
                                    <div class="alert alert-success" role="alert">
                                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
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

                                <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
                                    @csrf

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            {{ __('Resend the verification email') }}
                                        </button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                    @csrf

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-link btn-user btn-block">
                                            {{ __('Log Out') }}
                                        </button>
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
@endsection
