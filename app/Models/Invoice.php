<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'amount',
        'payment_status',
        'status',
    ];
    const PAYMENT_STATUS = ['paid', 'not_paid'];
    const STATUS = ['pending', 'on_progress', 'shipped', 'delivered', 'rejected', 'canceled_by_user', 'canceled_by_admin'];

    protected $related_search = ['customer.name'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }



    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_products')
            ->withPivot('quantity', 'price','total')
            ->withTimestamps();
    }
}
