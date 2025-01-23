<?php

use App\Http\Controllers\RouterOSAuditSystem\AuditController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuditController::class, 'index']);
