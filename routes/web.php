<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    runBackgroundJob('App\\Jobs\\ArithmeticJob', 'handle', [5, 10, 'add'], 3);
});
