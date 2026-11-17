<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Auth;
use DB;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class InvoiceController extends Controller
{

    public function index()
    {
        $invoices = Auth::user()->invoices()->with(['items', 'client',])->simplePaginate(10);
        return InvoiceResource::collection($invoices);
    }

    public function store(InvoiceStoreRequest $request)
    {
        $validated = $request->validated();
        $invoice = DB::transaction(function () use ($validated) {
            $validated["user_id"] = Auth::id();
            $invoice = Invoice::create($validated);
            $totalammonut = 0;
            foreach ($validated["items"] as $dataitem) {
                $itemtotal = $dataitem["price"] * $dataitem["quantity"];
                $dataitem["total"] = $itemtotal;
                $invoice->items()->create($dataitem);
                $totalammonut += $itemtotal;
            }
            $invoice->update([
                "total_ammount" => $totalammonut
            ]);
            return $invoice->load(['client', 'items']);
        });
        return response()->json([
            "msg" => "invoice created successfully",
            "data" => new InvoiceResource($invoice)
        ], 201);
    }
    public function show($InvoiceId)
    {
        $invioce = Auth::user()->invoices()->with('items')->where('id', $InvoiceId)->firstOrFail();
        return new InvoiceResource($invioce);
    }

    public function update(InvoiceUpdateRequest $request, $InvoiceId)
    {
        $validated = $request->validated();
        /**
         * @var Invoice  */
        $invoice = Auth::user()->invoices()->with(['items'])->where('id', $InvoiceId)->first();
        if (!$invoice) {
            return response()->json([
                "msg" => "invoice not found"
            ], 404);
        }
        DB::transaction(function () use ($invoice, $validated) {
            $invoice->update($validated);
            if (isset($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    if (isset($item['id'])) {  // frontend should sent old id
                        $invoice->items()->where('id', $item['id'])->update([
                            'item_name' => $item['item_name'] ?? DB::raw('item_name'),
                            'quantity' => $item['quantity'] ?? DB::raw('quantity'),
                            'price' => $item['price'] ?? DB::raw('price'),
                            'total' => $item['total'] ?? DB::raw('total')
                        ]);
                    } else {
                        $invoice->items()->create($item);
                    }
                }
            }
        });
        return response()->json([
            "msh" => "invoice with items updated successfully",
            "invoice" => new InvoiceResource($invoice)
        ], 201);
    }

    public function destroy(string $InvoiceId)
    {
        /**
         * @var Invoice
         */
        $invoice = Auth::user()->invoices()->where('id', $InvoiceId)->firstOrFail();
        $invoice->delete();
        return response()->json([
            "msg" => "deleted successfully"
        ], 200);
    }
    public function deleteitem($InvoiceId, $ItemId)
    {
        /**
         * @var Invoice 
         */
        /**
         * @var InvoiceItem
         */
        $invoice = Auth::user()->invoices()->where('id', $InvoiceId)->firstOrFail();
        $item = $invoice->items()->where('id', $ItemId)->firstOrFail();
        if ($invoice->items()->count() <= 1) {
            return response()->json([
                "msg" => "cannot delete the remining item . An invoice must have at lest one item"
            ], 422);
        }
        $item->delete();
        return response()->json([
            "msg" => "item deleted successfully"
        ], 200);

    }
}
