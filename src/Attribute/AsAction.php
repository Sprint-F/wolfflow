<?php

namespace SprintF\Bundle\Wolfflow\Attribute;

/**
 * Атрибут, указывающий на то, что помеченный им класс является действием бизнес-процесса.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AsAction
{
    public function __construct(
        /** Символьное имя бизнес-процесса, которому принадлежит действие */
        public string $workflow,

        /** Собственное имя действия */
        public ?string $name = null,
    ) {
    }
}
