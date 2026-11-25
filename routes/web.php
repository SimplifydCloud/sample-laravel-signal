<?php

use App\Http\Controllers\IdeaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', [IdeaController::class, 'index'])->name('ideas.index');
Route::post('/ideas', [IdeaController::class, 'store'])->middleware('throttle:10,1')->name('ideas.store');
Route::post('/ideas/{idea}/vote', [IdeaController::class, 'vote'])->middleware('throttle:30,1')->name('ideas.vote');
Route::get('/ready', function () {
    try {
        DB::table('ideas')->exists();

        return response()->json(['status' => 'ready']);
    } catch (Throwable) {
        return response()->json(['status' => 'unavailable'], 503);
    }
});
