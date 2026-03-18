<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('snippets.index'));

Route::get('/snippets', fn () => view('snippets.index'))->name('snippets.index');
