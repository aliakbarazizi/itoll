<?php

namespace App\Models\Enums;

enum OrderStatus: string
{
    case REGISTERED = 'registered';
    case IN_PROGRESS = 'in_progress';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}
