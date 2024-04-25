<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel, HasColor, HasIcon
{
    case NEW = 'new';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case DELIVERED = 'delivered';
    case SHIPPED = 'shipped';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NEW => 'New',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::DELIVERED => 'Delivered',
            self::SHIPPED => 'Shipped',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::NEW => 'gray',
            self::PROCESSING => 'warning',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
            self::DELIVERED => 'info',
            self::SHIPPED => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::NEW => 'heroicon-o-sparkles',
            self::PROCESSING => 'heroicon-o-arrow-path',
            self::COMPLETED => 'heroicon-o-check',
            self::CANCELLED => 'heroicon-o-x-circle',
            self::DELIVERED => 'heroicon-o-check-circle',
            self::SHIPPED => 'heroicon-o-truck',
        };
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::DELIVERED => 'Delivered',
            self::SHIPPED => 'Shipped',
        };
    }

    public function getColorHtml(): ?string
    {
        return match ($this) {
            self::NEW => 'bg-gray-500',
            self::PROCESSING => 'bg-amber-500',
            self::COMPLETED => 'bg-green-500',
            self::CANCELLED => 'bg-red-500',
            self::DELIVERED => 'bg-blue-500',
            self::SHIPPED => 'bg-teal-500',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded text-white shadow '.$this->getColorHtml().'">'.$this->getLabelText().'</span>';
    }
}
