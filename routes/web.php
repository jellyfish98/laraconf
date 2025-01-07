<?php

use App\Livewire\ConferenceSignUpPage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/conference-sign-up', ConferenceSignUpPage::class)->name('conference-sign-up');
