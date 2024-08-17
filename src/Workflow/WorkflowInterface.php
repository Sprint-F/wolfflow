<?php

namespace SprintF\Bundle\Wolfflow\Workflow;

// use SprintF\Bundle\Workflow\ActionLog\ActionLogEntryInterface;
// use SprintF\Bundle\Workflow\Workflow\WorkflowInterface;
use SprintF\Bundle\Wolfflow\Action\ActionCollection;

/**
 * Общий интерфейс для всех объектов бизнес-процессов.
 */
interface WorkflowInterface
{
    /**
     * Метод, возвращающий имя этого бизнес-процесса.
     */
    public static function getDefaultName(): string;

    /**
     * Метод для передачи коллекции всех действий.
     * Должен выполняться на этапе компиляции.
     */
    public function setActions(ActionCollection $actions): void;

    /**
     * Все действия данного бизнес-процесса.
     */
    public function getActions(): array;
}
