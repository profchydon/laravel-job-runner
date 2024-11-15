<?php

use App\Http\Controllers\JobLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('jobs')->group(function () {
    Route::get('/', [JobLogController::class, 'index'])->name('admin.jobs.index');
});
