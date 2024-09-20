<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToDoListController;


Route::get('/', [ToDoListController::class, 'index']);
Route::post('/tasks', [ToDoListController::class, 'store']);
Route::delete('/tasks/{task}', [ToDoListController::class, 'destroy']);
Route::patch('/tasks/{task}', [ToDoListController::class, 'update']);
Route::get('/tasks', [ToDoListController::class, 'showAll']);


