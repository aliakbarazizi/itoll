<?php

namespace App\Models;

use App\Models\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $user_id
 * @property int $from_customer_id
 * @property int $to_customer_id
 * @property OrderStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer $fromCustomer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderLocation> $orderLocations
 * @property-read int|null $order_locations_count
 * @property-read \App\Models\Customer $toCustomer
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFromCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereToCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @property int|null $driver_id
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDriverId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function toCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderLocations(): HasMany
    {
        return $this->hasMany(OrderLocation::class);
    }
}
