<?php

namespace SprintF\Bundle\Wolfflow\Entity;

// use SprintF\Bundle\Workflow\ActionLog\ActionLogEntryInterface;
// use SprintF\Bundle\Workflow\Workflow\WorkflowInterface;

/**
 * Общий объект для всех сущностей всех бизнес-процессов.
 */
interface WorkflowEntityInterface
{
    /**
     * Объект бизнес-процесса, которому принадлежит сущность.
     */
    public static function getWorkflow()/* :WorkflowInterface */;

    /**
     * Имя класса сущности. Почти всегда это будет именно класс в смысле PHP-класса.
     */
    public function getEntityClass(): string;

    /**
     * Уникальный идентификатор сущности внутри ее класса.
     * Может быть строкой или числом.
     * null предусматривается на случай "новой" сущности, еще не получившей идентификатор.
     */
    public function getEntityId(): int|string|null;

    /**
     * Вообще говоря, этот метод не нужен. Но он требуется нам для фикса поведения сущностей Doctrine в отдельных случаях,
     * когда Doctrine присваивает идентификатор записи, которая, по факту, не была сохранена...
     */
    public function setEntityId(int|string|null $id);
}
