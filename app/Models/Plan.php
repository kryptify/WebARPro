<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    public function subscription()
    {
        return $this->hasMany(Subscription::class, 'stripe_id', 'stripe_price' );
    }

}
