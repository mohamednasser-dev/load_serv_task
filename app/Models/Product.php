<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
    ];

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_products')
            ->withPivot('quantity', 'price','total')
            ->withTimestamps();
    }

}
