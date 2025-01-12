<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});


Route::get('/{pathMatch}', function () {
    return view('home');
})->where('pathMatch', '.*');
