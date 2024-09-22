<?php

namespace Homeful\Property\Enums;

enum DevelopmentType
{
    case BP_957;
    case BP_220;

    public function getName(): string
    {
        return match ($this) {
            self::BP_957 => 'BP 957',
            self::BP_220 => 'BP 220',
        };
    }
}
