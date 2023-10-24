<?php

namespace App\Models\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case DRIVER = 'driver';
}
