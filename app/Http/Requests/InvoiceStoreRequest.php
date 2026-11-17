<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'title' => 'nullable|string|max:255',
            'status' => 'required|in:draft,sent,paid ,overdue',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            // 'total_ammount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            //invoice_item//
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|min:1|integer',
            'items.*.price' => 'required|numeric|min:0',
            // 'items.*.total' => 'required|numeric|min:0'
        ];
    }
}
