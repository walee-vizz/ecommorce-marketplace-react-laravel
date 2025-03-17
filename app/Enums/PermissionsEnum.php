<?php

namespace App\Enums;

enum PermissionsEnum: String
{
    case ApproveVendors = 'ApproveVendors';

    case BuyProducts = 'BuyProducts';

    case SellProducts = 'SellProducts';
    // Add more permissions as needed
}
