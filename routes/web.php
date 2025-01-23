<?php

use App\Http\Controllers\RouterOSAuditSystem\AuditController;
use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    return view('welcome');
});
