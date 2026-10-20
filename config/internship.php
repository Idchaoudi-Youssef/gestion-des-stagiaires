<?php

/*
|--------------------------------------------------------------------------
| Internship certificate
|--------------------------------------------------------------------------
|
| The details printed on the internship certificate of the intern. They are
| read from the environment so the same code can be used by two different
| companies without touching the views.
|
*/

return [

    'company' => [
        'name' => env('COMPANY_NAME', 'Company Name'),
        'city' => env('COMPANY_CITY', 'City'),
        'phone' => env('COMPANY_PHONE', '00 00 00 00 00'),
        'email' => env('COMPANY_EMAIL', 'contact@example.com'),
        'siret' => env('COMPANY_SIRET', '000 000 000 00000'),
    ],

    'signatory' => [
        'name' => env('SIGNATORY_NAME', 'Signatory Name'),
        'position' => env('SIGNATORY_POSITION', 'Company Director'),
    ],

];
