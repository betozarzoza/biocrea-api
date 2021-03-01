<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursePurchase extends Model
{
    use HasFactory;
    protected $table = 'courses_purchases';

    public function module() {
        return $this->belongsTo(Module::class, 'product_id');
    }
}