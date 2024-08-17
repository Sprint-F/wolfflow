<?php

namespace SprintF\Bundle\Wolfflow\Attribute;

/**
 * Атрибут, указывающий на то, что помеченный им класс является объектом бизнес-процесса.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AsWorkflow
{
    public function __construct(
        /** Собственное имя бизнес-процесса */
        public string $name,
    ) {
    }
}
