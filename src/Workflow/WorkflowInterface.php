<?php

namespace SprintF\Bundle\Wolfflow\Workflow;

// use SprintF\Bundle\Workflow\ActionLog\ActionLogEntryInterface;
// use SprintF\Bundle\Workflow\Workflow\WorkflowInterface;
use SprintF\Bundle\Wolfflow\Action\ActionCollection;
use SprintF\Bundle\Wolfflow\Status\StatusCollection;

/**
 * Общий интерфейс для всех объектов бизнес-процессов.
 */
interface WorkflowInterface
{
    /**
     * Метод, возвращающий символьное имя данного бизнес-процесса.
     */
    public static function getName(): string;

    /**
     * Метод для передачи коллекции всех действий.
     * Используется на этапе компиляции.
     */
    public function setActions(ActionCollection $actions): void;

    /**
     * Все действия данного бизнес-процесса.
     */
    public function getActions(): array;

    /**
     * Метод для передачи коллекции всех статусов.
     * Используется на этапе компиляции.
     */
    public function setStatuses(StatusCollection $statuses): void;

    /**
     * Все статусы данного бизнес-процесса.
     */
    public function getStatuses(): array;
}
