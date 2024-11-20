<?php

use App\Http\Controllers\TasksController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tasks', TasksController::class, ['except' => 'show']);
