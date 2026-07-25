<?php

namespace SprintF\Bundle\Wolfflow\LogEntry;

use SprintF\Bundle\Wolfflow\Action\ActionInterface;
use SprintF\Bundle\Wolfflow\Action\ActionResult;
use SprintF\Bundle\Wolfflow\Actor\ActorInterface;
use SprintF\Bundle\Wolfflow\Context\ContextInterface;
use SprintF\Bundle\Wolfflow\Entity\EntityInterface;

/**
 * Общий интерфейс для всех классов, представляющих собой записи в логе действий.
 */
interface LogEntryInterface
{
    /**
     * Действие, которое производилось над сущностью.
     */
    public function getAction(): ActionInterface;

    public function getActionClass(): string;

    public function getActionName(): string;

    public function setAction(ActionInterface $action);

    /**
     * Сущность, над которой производилось действие.
     */
    public function getEntity(): EntityInterface;

    public function getEntityClass(): string;

    public function getEntityId(): int|string|\Stringable|null;

    public function setEntity(EntityInterface $entity);

    /**
     * Контекст действия.
     */
    public function getContext(): ContextInterface;

    public function setContext(ContextInterface $context);

    /**
     * Актор, производивший действие.
     */
    public function getActor(): ?ActorInterface;

    public function setActor(?ActorInterface $actor);

    /**
     * Дата и время начала выполнения действия.
     */
    public function getStartedAt(): ?\DateTimeInterface;

    public function setStartedAt(?\DateTimeInterface $startedAt);

    /**
     * Дата и время окончания выполнения действия.
     */
    public function getFinishedAt(): ?\DateTimeInterface;

    public function setFinishedAt(?\DateTimeInterface $finishedAt);

    /**
     * Результат выполнения действия.
     */
    public function getResult(): ActionResult;

    public function setResult(ActionResult $result);

    /**
     * Подробности о результате выполнения действия.
     */
    public function getReason(): ?string;

    public function setReason(?string $reason);
}
