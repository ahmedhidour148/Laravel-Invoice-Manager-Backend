<?php

namespace App\Observers;

use App\Models\InvoiceItem;

class InvoiceItemServe
{
    public function creating(InvoiceItem $invoiceItem): void
    {
        // $invoiceItem->total = $invoiceItem->quantity * $invoiceItem->price;
    }

    public function updating(InvoiceItem $invoiceItem): void
    {
        // if ($invoiceItem->isDirty('quantity') || $invoiceItem->isDirty('price')) {
        //     $invoiceItem->total = $invoiceItem->quantity * $invoiceItem->price;
        // }
    }
    public function saved(InvoiceItem $invoiceItem): void
    {
        // $this->calulatetotalpriceinvoice($invoiceItem);
    }

    public function calulatetotalpriceinvoice(InvoiceItem $invoiceItem): void
    {
        // $invoice = $invoiceItem->invoice;
        // $totalsum = $invoice->items()->sum('total');
        // $invoice->updateQuielty([
        //     'total_ammount'=>$totalsum
        // ]);
    }
}
