<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel, HasColor, HasIcon
{
    case PAID = 'paid';
    case PENDING = 'pending';
    case FAILED = 'failed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PAID => 'Paid',
            self::PENDING => 'Pending',
            self::FAILED => 'Failed',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PAID => 'success',
            self::PENDING => 'warning',
            self::FAILED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PAID => 'heroicon-o-check-circle',
            self::PENDING => 'heroicon-o-clock',
            self::FAILED => 'heroicon-o-x-circle',
        };
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::PAID => 'Paid',
            self::PENDING => 'Pending',
            self::FAILED => 'Failed',
        };
    }

    public function getColorHtml(): string{
        return match ($this) {
            self::PAID => 'bg-green-500',
            self::PENDING => 'bg-slate-600',
            self::FAILED => 'bg-red-500',
        };
    }

    public function getLabelHtml(): string
    {
        return '<span class="py-1 px-3 rounded ' . $this->getColorHtml() . ' shadow text-white">' . $this->getLabelText() . '</span>';
    }
}
