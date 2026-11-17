<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        // 'invoice_number',
        'title',
        'user_id',
        'client_id',
        'status',
        'issue_date',
        'due_date',
        'total_ammount',
        'notes'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
