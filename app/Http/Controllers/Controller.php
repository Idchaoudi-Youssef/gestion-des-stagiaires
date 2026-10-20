<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    /*
    |--------------------------------------------------------------------------
    | Authorisation and validation
    |--------------------------------------------------------------------------
    |
    | The two traits give every controller the "authorize()" and "validate()"
    | helpers used by the policies and the form requests.
    |
    */

    use AuthorizesRequests, ValidatesRequests;
}
