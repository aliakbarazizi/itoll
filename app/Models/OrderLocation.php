<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\OrderLocation
 *
 * @property int $id
 * @property int $order_id
 * @property string $latitude
 * @property string $longitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLocation whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLocation whereUpdatedAt($value)
 * @method static \Database\Factories\OrderLocationFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class OrderLocation extends Model
{
    use HasFactory;

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    protected $fillable = [
        'latitude',
        'longitude',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
