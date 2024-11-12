<?php

namespace SprintF\Bundle\Wolfflow\Dbal;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class ActionResultType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'action_result';
    }

    public function getName(): string
    {
        return 'action_result';
    }
}
