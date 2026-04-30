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
    public function cartValidator(Request $request)
    {
        $validated = $request->validate([
            'input' => 'required|array',
            'input.*.id' => 'required|integer',
            'input.*.required' => 'required|boolean',
            'input.*.done' => 'required|boolean'
        ]);
        $valid = false;
        $input = $validated['input'];
        $invalidItems = [];
        foreach ($input as $item) {
            if ($item['required'] && !$item['done']) {
                $invalidItems[] = $item['id'];
                $valid = false;
            }
        }
        if (empty($invalidItems)) {
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
                'invalid_items' => $invalidItems
            ],
            'error' => null,
        ]);
    }

    public function vendorAllocation(Request $request)
    {
        $validated = $request->validateWithBag('vendorAllocation', [
            'input' => 'required|array',
            'input.order_qty' => 'required|integer|min:1',
            'input.vendors' => 'required|array',
            'input.vendors.*.id' => 'required|integer',
            'input.vendors.*.stock' => 'required|integer|min:0',
        ]);
        $input = $validated['input'];
        $orderQty = $input['order_qty'];
        $vendors = $input['vendors'];
        $allocations = [];
        foreach ($vendors as $vendor) {
            if ($orderQty <= 0) {
                break;
            }
            if ($vendor['stock'] <= 0) {
                continue;
            }
            $allocated = min($orderQty, $vendor['stock']);
            $allocations[] = [
                'vendor_id' => $vendor['id'],
                'allocated' => $allocated
            ];
            $orderQty -= $allocated;
        }

        if ($orderQty > 0) {
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => 'Not  enough stock to fulfill order'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $allocations,
            'error' => null
        ]);
    }
}
