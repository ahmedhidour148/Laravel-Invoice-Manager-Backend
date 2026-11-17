<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'exists:clients,id',
            'title' => 'string|max:255',
            'status' => 'in:draft,sent,paid ,overdue',
            'issue_date' => 'date',
            'due_date' => 'date|after_or_equal:issue_date',
            // 'total_ammount' => 'numeric|min:0',
            'notes' => 'nullable|string',
            //invoice_item//
            'items' => 'array|min:1',
            'item.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.item_name' => 'string|max:255',
            'items.*.quantity' => 'min:1|integer',
            'items.*.price' => 'numeric|min:0',
            // 'items.*.total' => 'numeric|min:0'
        ];
    }
}
