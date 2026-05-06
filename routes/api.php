<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExerciseController;

Route::post('/exercise-1-artwork-version', function (Request $request) {
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

Route::post('/exercise-2-tier-pricing', [ExerciseController::class, 'tierPricing']);
Route::post('/exercise-3-cart-validator', [ExerciseController::class, 'cartValidator']);
Route::post('/exercise-4-vendor-allocation', [ExerciseController::class, 'vendorAllocation']);
Route::post('/exercise-5-discount', [ExerciseController::class, 'discount']);
Route::post('/exercise-6-approval-flow', [ExerciseController::class, 'approvalFlow']);
Route::post('/exercise-7-inventory', [ExerciseController::class, 'inventory']);

