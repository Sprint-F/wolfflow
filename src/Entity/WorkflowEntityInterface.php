<?php

namespace SprintF\Bundle\Wolfflow\Entity;

// use SprintF\Bundle\Workflow\ActionLog\ActionLogEntryInterface;

/**
 * Общий объект для всех сущностей всех бизнес-процессов.
 */
interface WorkflowEntityInterface
{
    /**
     * Метод, возвращающий символьное имя бизнес-процесса, которому принадлежит данное сущность.
     * По умолчанию метод должен возвращать значение свойства атрибута WorkflowEntity::workflow.
     */
    public function getDefaultWorkflowName(): string;

    /**
     * Имя класса сущности. Почти всегда это будет именно класс в смысле PHP-класса.
     */
    public function getEntityClass(): string;

    /**
     * Уникальный идентификатор сущности внутри ее класса.
     * Может быть строкой или числом.
     * null предусматривается на случай "новой" сущности, еще не получившей идентификатор.
     */
    public function getEntityId(): int|string|\Stringable|null;

    /**
     * Вообще говоря, этот метод не нужен. Но он требуется нам для фикса поведения сущностей Doctrine в отдельных случаях,
     * когда Doctrine присваивает идентификатор записи, которая, по факту, не была сохранена...
     *
     * @return $this
     */
    public function setEntityId(int|string|\Stringable|null $id): static;

    /**
     * Метод, отвечающий на вопрос "Является ли сущность новой"?
     * Новой считаем сущность, которая еще не сохранена в хранилище.
     */
    public function isNew(): bool;
}
