<?php

namespace SprintF\Bundle\Wolfflow\Status;

use SprintF\Bundle\Wolfflow\Entity\EntityInterface;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowInterface;

/**
 * Общий интерфейс для всех статусов всех бизнес-процессов.
 */
interface StatusInterface
{
    /**
     * Метод, возвращающий имя бизнес-процесса, которому принадлежит данный статус.
     * По умолчанию метод должен возвращать значение свойства атрибута AsStatus::$workflow.
     */
    public static function getWorkflowName(): string;

    /**
     * Метод, возвращающий объект бизнес-процесса, которому принадлежит данный статус.
     */
    public function getWorkflow(): WorkflowInterface;

    /**
     * Сущность, статус которой мы хотим определить.
     */
    public function setEntity(EntityInterface $entity);

    /**
     * Сущность, статус которой мы хотим определить.
     */
    public function getEntity(): EntityInterface;

    /**
     * Непосредственно метод определения статуса.
     */
    public function __invoke(): bool;
}
