<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'invoice_no',
        'user_id',
        'order_type',
        'table_number',
        'note',
        'total',
        'payment_method',
        'cash_received',
        'change_amount',
        'status',
        'void_reason',
        'voided_by',
        'voided_at',
    ];

    protected $casts = ['voided_at' => 'datetime'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voidedBy()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }
}
