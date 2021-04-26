<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 * @property-read int id
 * @property int|null user_id
 * @property int total_amount
 * @property int subtotal_amount
 * @property int|null promo_id
 * @property boolean promo_applied
 *
 * @property-read User user
 * @property-read Collection orderProducts
 * @property-read Promo promo
 *
 */
class Order extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function promo(): BelongsTo
	{
		return $this->belongsTo(Promo::class);
	}

}
