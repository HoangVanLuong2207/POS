<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_id',
        'total_amount',
        'discount',
        'final_amount',
        'payment_method',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')->withPivot('quantity', 'price');
    }
}
