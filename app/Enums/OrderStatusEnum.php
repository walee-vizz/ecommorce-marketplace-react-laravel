<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatusEnum: String implements HasColor, HasIcon, HasLabel
{

    case Draft = 'draft';

    case Pending = 'pending';

    case Confirmed = 'confirmed';

    case Processing = 'processing';

    case Shipped = 'shipped';

    case OutForDelivery = 'out for delivery';

    case Delivered = 'delivered';

    case Paid = 'paid';

    case Refunded = 'refunded';

    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Shipped => 'Shipped',
            self::OutForDelivery => 'Out For Delivery',
            self::Delivered => 'Delivered',
            self::Refunded => 'Refunded',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Draft => 'info',
            self::Pending => 'primary',
            self::Confirmed, self::Processing => 'info',
            self::Shipped, self::OutForDelivery, self::Delivered => 'success',
            self::Cancelled, self::Refunded => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft          => 'heroicon-m-clipboard-document-list',       // Icon for draft items
            self::Pending        => 'heroicon-m-arrow-path',      // Icon for in-progress status
            self::Confirmed      => 'heroicon-m-check-circle',    // Confirmation icon
            self::Processing     => 'heroicon-m-arrow-path',      // Icon for in-progress status
            self::Shipped        => 'heroicon-m-truck',           // Shipping icon
            self::OutForDelivery => 'heroicon-m-clock',           // Delivery timing icon
            self::Delivered      => 'heroicon-m-check-badge',     // Delivered/success icon
            self::Cancelled      => 'heroicon-m-x-circle',        // Cancelled icon
            self::Refunded       => 'heroicon-m-currency-dollar', // Currency dollar icon for refunded status
        };
    }
}
