<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property-read int id
 * @property string name
 * @property string type
 * @property int value
 * @property string code
 *
 * @property-read Collection orders
 */
class Promo extends Model
{
    use HasFactory;

    public function orders(): HasMany
	{
		return $this->hasMany(Order::class);
	}
}
