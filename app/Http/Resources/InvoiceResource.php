<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "invoice_number" => $this->invoice_number,
            "title" => $this->title,
            "status" => $this->status,
            "total_ammount" => $this->total_ammount,
            "items" => InvoiceItemsResource::collection($this->whenLoaded('items')),
            "client" => new ClientResource($this->whenLoaded('client')),
            "issue_date" => $this->issue_date,
            "due_date" => $this->due_date,
            "notes" => $this->notes
        ];
    }
}
