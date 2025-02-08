<?php

namespace SprintF\Bundle\Wolfflow\Attribute;

/**
 * Атрибут, указывающий на то, что помеченный им класс является сущностью бизнес-процесса.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AsEntity
{
    public function __construct(
        /** Символьное имя бизнес-процесса, в котором фигурирует сущность */
        public string $workflow,
        /** Класс записи в логе действий над данными сущностями */
        public string $logEntryClass,
    ) {
    }
}
