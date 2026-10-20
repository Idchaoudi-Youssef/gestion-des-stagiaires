<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} &mdash; {{ __('Account pending') }}</title>
    <style>
        body {
            background-color: #fbfbfb;
            font-family: "Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .message-box {
            max-width: 420px;
            margin: 120px auto 0;
            padding: 0 20px;
            color: #000000;
            text-align: center;
        }
        .message-box h1 {
            font-size: 42px;
            line-height: 46px;
            margin-bottom: 24px;
        }
        .message-box p {
            color: #5a5a5a;
            font-size: 16px;
            line-height: 1.6;
        }
        .buttons-con .action-link-wrap {
            margin-top: 32px;
        }
        .buttons-con .action-link-wrap a,
        .buttons-con .action-link-wrap button {
            background-color: #4e73df;
            padding: 10px 25px;
            border-radius: 4px;
            color: #FFF;
            font-weight: bold;
            font-size: 14px;
            transition: all 0.3s linear;
            cursor: pointer;
            border: none;
            margin-right: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .buttons-con .action-link-wrap a:hover,
        .buttons-con .action-link-wrap button:hover {
            background-color: #5A5C6C;
            color: #fff;
        }
        .buttons-con .action-link-wrap .logout-link {
            background-color: transparent;
            color: #4e73df;
            padding-left: 0;
        }
    </style>
</head>
<body>
<div class="message-box">
    <h1>Hello {{ Auth::user()->name }}</h1>
    <p>
        Your account has been created and is waiting for an administrator to validate it.
        You will be able to reach the platform as soon as your role has been confirmed.
    </p>

    <div class="buttons-con">
        <div class="action-link-wrap">
            <a href="{{ route('profile') }}">{{ __('My profile') }}</a>

            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-link">{{ __('Log out') }}</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
