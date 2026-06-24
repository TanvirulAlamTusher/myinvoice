<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ProductReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;




class ProductReturnController extends Controller
{


public function storeReturn(Request $request, Invoice $invoice)
{
    $validated = $request->validate([
        'items'                   => 'required|array|min:1',
        'items.*.invoice_item_id' => 'required|integer|exists:invoice_items,id',
        'items.*.quantity'        => 'required|numeric|min:0.01',
        'items.*.unit_price'      => 'required|numeric|min:0',
        'items.*.subtotal'        => 'required|numeric|min:0',
        'reason'                  => 'nullable|string|max:100',
        'return_method'           => 'nullable|string|max:100',
        'note'                    => 'nullable|string|max:1000',
    ]);

    // ── Validate: quantities cannot exceed sold qty ───────────
    foreach ($validated['items'] as $reqItem) {
        $invoiceItem = $invoice->items()->find($reqItem['invoice_item_id']);

        if (! $invoiceItem) {
            return response()->json([
                'message' => 'Item #' . $reqItem['invoice_item_id'] . ' does not belong to this invoice.',
            ], 422);
        }

        if ($reqItem['quantity'] > $invoiceItem->quantity) {
            return response()->json([
                'message' => "Return quantity for \"{$invoiceItem->product_name}\" exceeds sold quantity ({$invoiceItem->quantity}).",
            ], 422);
        }
    }

    DB::transaction(function () use ($invoice, $validated) {

        $totalAmount = collect($validated['items'])->sum('subtotal');

        // ── One ProductReturn per invoice (updateOrCreate) ────
        $productReturn = ProductReturn::updateOrCreate(
            ['invoice_id' => $invoice->id],                   // match on invoice_id
            [
                'created_by'    => auth()->id(),
                'return_no'     => ProductReturn::where('invoice_id', $invoice->id)->value('return_no')
                                    ?? 'RET-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'return_date'   => now(),
                'reason'        => $validated['reason']        ?? null,
                'return_method' => $validated['return_method'] ?? null,
                'status'        => 'completed',
                'total_amount'  => $totalAmount,
                'note'          => $validated['note']          ?? null,
            ]
        );

        // Build a keyed map: invoice_item_id => new quantity
        $newQtyMap = collect($validated['items'])->keyBy('invoice_item_id');

        // ── Sync return items & restore stock delta ───────────
        foreach ($invoice->items as $invoiceItem) {
            $existingReturnItem = $productReturn->items()
                ->where('invoice_item_id', $invoiceItem->id)
                ->first();

            $previousQty = $existingReturnItem?->quantity ?? 0;

            if (isset($newQtyMap[$invoiceItem->id])) {
                // This item is in the new return payload
                $reqItem  = $newQtyMap[$invoiceItem->id];
                $newQty   = $reqItem['quantity'];
                $qtyDelta = $newQty - $previousQty;   // positive = more returned, negative = fewer

                // updateOrCreate the return item row
                $productReturn->items()->updateOrCreate(
                    ['invoice_item_id' => $invoiceItem->id],
                    [
                        'product_id'   => $invoiceItem->product_id,
                        'product_name' => $invoiceItem->product_name,
                        'quantity'     => $newQty,
                        'unit_price'   => $reqItem['unit_price'],
                        'subtotal'     => $reqItem['subtotal'],
                    ]
                );

                // Restore stock by the delta (only if product exists)
                if ($invoiceItem->product && $qtyDelta != 0) {
                    $invoiceItem->product->increment('stock', $qtyDelta);
                }

            } else {
                // Item was in a previous return but is NOT in this update —
                // remove the return item and give the stock back to zero delta
                if ($existingReturnItem) {
                    // Undo the previously returned stock
                    if ($invoiceItem->product && $previousQty > 0) {
                        $invoiceItem->product->decrement('stock', $previousQty);
                    }
                    $existingReturnItem->delete();
                }
            }
        }
    });

    return response()->json(['message' => 'Return saved successfully.'], 200);
}

public function destroyReturn(Invoice $invoice)
{
    $productReturn = ProductReturn::where('invoice_id', $invoice->id)->first();

    if (! $productReturn) {
        return response()->json(['message' => 'No return record found.'], 404);
    }

    DB::transaction(function () use ($productReturn, $invoice) {

        // Reverse stock for every returned item
        foreach ($productReturn->items as $returnItem) {
            $invoiceItem = $invoice->items()->find($returnItem->invoice_item_id);

            if ($invoiceItem?->product && $returnItem->quantity > 0) {
                // Returned qty was added to stock when return was created — undo it
                $invoiceItem->product->decrement('stock', $returnItem->quantity);
            }
        }

        // Delete items then the parent return row
        $productReturn->items()->delete();
        $productReturn->delete();
    });

    return response()->json(['message' => 'Return cancelled successfully.'], 200);
}
}
