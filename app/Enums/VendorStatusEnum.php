<?php

namespace App\Enums;

enum VendorStatusEnum: string
{
    case Pending = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';


}
