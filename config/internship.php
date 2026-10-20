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
| No default value contains the identity of a real company: fill the
| variables below in your own ".env" file, which is never committed.
|
*/

return [

    'company' => [
        'name' => env('COMPANY_NAME', env('APP_NAME', 'Gestion des Stagiaires')),
        'city' => env('COMPANY_CITY', ''),
        'phone' => env('COMPANY_PHONE', ''),
        'email' => env('COMPANY_EMAIL', ''),
        'siret' => env('COMPANY_SIRET', ''),

        // Path of the logo inside the "public" directory.
        'logo' => env('COMPANY_LOGO', 'img/logo-placeholder.svg'),
    ],

    'signatory' => [
        'name' => env('SIGNATORY_NAME', ''),
        'position' => env('SIGNATORY_POSITION', ''),

        // Optional: path of the signature image inside the "public"
        // directory. Leave it empty to print the certificate without it.
        'image' => env('SIGNATURE_IMAGE', ''),
    ],

];
