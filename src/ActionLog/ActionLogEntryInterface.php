<?php

namespace SprintF\Bundle\Wolfflow\ActionLog;

use SprintF\Bundle\Wolfflow\Action\ActionResult;
use SprintF\Bundle\Wolfflow\Actor\ActorInterface;
use SprintF\Bundle\Wolfflow\Context\ContextInterface;
use SprintF\Bundle\Wolfflow\Entity\WorkflowEntityInterface;

/**
 * Интерфейс, задающий требования к объекту, являющемуся записью в логе бизнес-действий.
 */
interface ActionLogEntryInterface
{
    /**
     * Последовательный монотонно возрастающий ID записи в логе бизнес-действий.
     */
    public function getId(): ?int;

    /**
     * Класс сущности, над которой было произведено действие.
     */
    public function getEntityClass(): string;

    /**
     * Идентификатор сущности, над которой было произведено действие.
     */
    public function getEntityId(): ?int;

    /**
     * Сущность, над которой было произведено действие.
     */
    public function getEntity(): ?WorkflowEntityInterface;

    public function setEntity(WorkflowEntityInterface $entity): static;

    /**
     * Класс действия, которое было произведено над сущностью.
     */
    public function getActionClass(): string;

    public function setActionClass(string $actionClass): static;

    /**
     * Контекст действия, которое было произведено над сущностью.
     */
    public function getContext(): ?ContextInterface;

    public function setContext(?ContextInterface $context): static;

    /**
     * Пользователь системы, сервис, процесс от которого производилось действие.
     */
    public function getActor(): ?ActorInterface;

    public function setActor(ActorInterface $user): static;

    /**
     * Дата и время начала выполнения действия.
     */
    public function getStartedAt(): ?\DateTimeInterface;

    public function setStartedAt(\DateTimeInterface $startedAt): static;

    /**
     * Дата и время окончания выполнения действия.
     */
    public function getFinishedAt(): ?\DateTimeInterface;

    public function setFinishedAt(\DateTimeInterface $finishedAt): static;

    /**
     * Результат выполнения действия.
     */
    public function getResult(): ActionResult;

    public function setResult(ActionResult $result): static;
}
