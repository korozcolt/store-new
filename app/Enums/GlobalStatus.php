<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum GlobalStatus: int implements HasLabel, HasColor, HasIcon
{
    case ENABLED = 1;
    case DISABLED = 0;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ENABLED => 'Enabled',
            self::DISABLED => 'Disabled',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ENABLED => 'success',
            self::DISABLED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ENABLED => 'heroicon-o-check',
            self::DISABLED => 'heroicon-o-x-circle',
        };
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::ENABLED => 'Enabled',
            self::DISABLED => 'Disabled',
        };
    }

    public function getColorHtml(): string
    {
        return match ($this) {
            self::ENABLED => 'bg-green-500',
            self::DISABLED => 'bg-red-500',
        };
    }

    public function getLabelHtml(): ?string
    {
        return '<span class="py-1 px-3 rounded '.$this->getColorHtml().' text-white shadow">'.$this->getLabelText().'</span>';
    }
}
