<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'sso/callback',
        'solist',       
        'mobile-app',
        'internal-po/pick',
        'insertdoc',
        'dashboarddoc',
        'store/checkout',
    ];
}