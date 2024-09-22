<?php

namespace Homeful\Property\Enums;

enum ProjectType
{
    case CONDOMINIUM;
    case DUPLEX;
    case ROW_HOUSE;
    case SINGLE_ATTACHED;
    case SINGLE_DETACHED;
    case QUADRUPLEX;

    public function getName(): string
    {
        return match ($this) {
            self::CONDOMINIUM => 'Condominium',
            self::DUPLEX => 'Duplex',
            self::ROW_HOUSE => 'Row House',
            self::SINGLE_ATTACHED => 'Single Attached',
            self::SINGLE_DETACHED => 'Single Detached',
            self::QUADRUPLEX => 'Quadruplex'
        };
    }
}
