<?php

namespace SprintF\Bundle\Wolfflow\Context;

/**
 * Типовая реализация класса контекста действия.
 */
abstract class ContextAbstract implements ContextInterface
{
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
