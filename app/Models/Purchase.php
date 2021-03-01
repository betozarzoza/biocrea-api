<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
