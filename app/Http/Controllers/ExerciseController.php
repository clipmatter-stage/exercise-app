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
        $validTiers = [];
        foreach ($tiers as $tier) {
            if ($quantity >= $tier['min']) {
                $validTiers[] = $tier;

            }
        }
        if (empty($validTiers)) {
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => 'Quantity does not meet the minimum requirement for any tier'
            ]);
        }
        $price = $validTiers[0]['price'];
        return response()->json([
            'success' => true,
            'data' => ['price' => $price],
            'error' => null
        ]);
    }
        public function cartValidator(Request $request){
        $validated =$request->validate([
            'input' => 'required|array',
            'input.*.id'=>'required|integer',
            'input.*.required'=>'required|boolean',
            'input.*.done'=>'required|boolean'
        ]);
        $valid = false;
        $input = $validated['input'];
        $invalidItems = [];
        foreach($input as $item){
            if($item['required'] && !$item['done']){
                $invalidItems[] = $item['id'];
                $valid =false;
            }
        }
        if(empty($invalidItems)){
            $valid = true;
            return response()->json([
                'success' => false,
                'data' => [
                    'valid' => $valid,
                    'invalid_items' => $invalidItems
                ],
                'error' => 'No invalid  Items Found',
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'valid' => $valid,
                'invalid_items' =>$invalidItems ],
            'error' => null,
        ]);
}
}
