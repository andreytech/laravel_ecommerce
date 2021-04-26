<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int id
 * @property string title
 * @property int price
 *
 * @property-read Collection orderProducts
 */
class Product extends Model
{
    use HasFactory;

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }
}
