<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ExerciseControlller;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::post('/exercise-1−artwork-version', function (Request $request) {
    $validated = $request->validate([
        'input' => 'required|array',
        'input.*.id' => 'required|integer',
        'input.*.time' => 'required|integer',
        'input.*.approved' => 'required|boolean',
        'input.*.rejected' => 'required|boolean',
    ]);
    $input = $validated['input'];
    $validItem = null;
    foreach ($input as $item) {
        if ($item['approved'] === true && $item['rejected'] === false) {
            if ($validItem === null) {
                $validItem = $item;
            } else if ($item['time'] >= $validItem['time'] && $item['id'] == max(array_column($input, 'id'))) {
                $validItem = $item;
            }
        }
    }
    if ($validItem) {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $validItem['id'],
            ],
            "error" => null
        ]);
    } else {
        return response()->json([
            'success' => false,
            'data' => null,
            "error" => "No valid item found"
        ]);
    }
});
Route::post('/exercise-2−tier-pricing', [ExerciseControlller::class, 'tierPricing']);
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
