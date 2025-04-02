<?php

namespace SprintF\Bundle\Wolfflow\Attribute;

/**
 * Атрибут, указывающий на то, что помеченный им класс является статусом бизнес-процесса.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AsStatus
{
    public function __construct(
        /** Символьное имя бизнес-процесса, которому принадлежит статус */
        public string $workflow,

        /** Собственное символьное имя статуса */
        public ?string $name = null,
    ) {
    }
}
