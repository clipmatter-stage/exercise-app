<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ExerciseController extends Controller
{
    public function tierPricing(Request $request)
    {
        $validated = $request->validate([
            'input.quantity' => 'required|integer',
            'input.tiers' => 'required|array',
            'input.tiers.*.min' => 'required|integer',
            'input.tiers.*.price' => 'required|numeric',
        ]);
        $input = $validated['input'];
        $quantity = $input['quantity'];
        $tiers = collect($input['tiers']);
        $tiers = $tiers->sortByDesc('min');
        $success = false;
        $error = null;
        foreach ($tiers as $tier) {
            if ($quantity >= $tier['min']) {
                $price = $tier['price'];
                $success = true;
                $error = null;
                return response()->json([
                    'success' => $success,
                    'data' => [ 'price' => $price ],
                    'error' => $error ?? null
                ]);
            }
        }
        return response()->json([
            'success' => $success,
            'data' => null,
            'error' => 'Quantity does not meet the minimum requirement for any tier'
        ]);
    }
}
