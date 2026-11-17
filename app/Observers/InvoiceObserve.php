<?php

namespace App\Observers;

use App\Models\Invoice;

class InvoiceObserve
{
    /**
     * Handle the Invoice "creating" event.
     */
    public function creating(Invoice $invoice): void
    {
        $last = Invoice::where('user_id', $invoice->user_id)->latest('id')->first();
        $next = $last ? $last->id + 1 : 1;
        $invoice->invoice_number = 'INV-' . str_pad($next, 3, '0', STR_PAD_LEFT);
        if ($invoice->status === 'sent') {
            $invoice->due_date = null;
        }
    }

    /**
     * Handle the Invoice "updating" event.
     */
    public function updating(Invoice $invoice): void
    {
        if ($invoice->isDirty('status')) {
            if ($invoice->status === 'sent') {
                $invoice->due_date = null;
            }

        }
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "restored" event.
     */
    public function restored(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     */
    public function forceDeleted(Invoice $invoice): void
    {
        //
    }
}
