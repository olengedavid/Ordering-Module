<?php

namespace App\Enums;

enum userType: string
{
    case SUPPLIER = 'supplier';
    case ADMIN = 'admin';
    case COMPANY = 'company';
}
