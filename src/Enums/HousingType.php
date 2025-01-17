<?php

namespace Homeful\Property\Enums;

enum HousingType
{
    case CONDOMINIUM;
    case DUPLEX;
    case ROW_HOUSE;
    case SINGLE_ATTACHED;
    case SINGLE_DETACHED;
    case QUADRUPLEX;
    case TOWNHOUSE;
    case TWIN_HOMES;

    public function getName(): string
    {
        return match ($this) {
            self::CONDOMINIUM => 'Condominium',
            self::DUPLEX => 'Duplex',
            self::ROW_HOUSE => 'Row House',
            self::SINGLE_ATTACHED => 'Single Attached',
            self::SINGLE_DETACHED => 'Single Detached',
            self::QUADRUPLEX => 'Quadruplex',
            self::TOWNHOUSE => 'Townhouse',
            self::TWIN_HOMES => 'Twin Homes'
        };
    }
}
