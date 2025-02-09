<?php

namespace SprintF\Bundle\Wolfflow\LogEntry;

use Doctrine\ORM\Mapping as ORM;
use SprintF\Bundle\Wolfflow\Action\ActionInterface;
use SprintF\Bundle\Wolfflow\Action\ActionResult;
use SprintF\Bundle\Wolfflow\Actor\ActorInterface;
use SprintF\Bundle\Wolfflow\Context\ContextInterface;
use SprintF\Bundle\Wolfflow\Entity\EntityInterface;

/**
 * Базовая реализация сущности LogEntry для Doctrine.
 *
 * Не забудьте добавить свойство $entity, указывающую на сущность, для которой записывается лог действий,
 * и свойство $actor, связывающее действие с тем, кто его произвел.
 */
trait LogEntryDoctrineTrait // implements LogEntryInterface
{
    private ?ActionInterface $action = null;

    #[ORM\Column(name: 'action_class', type: 'string', length: 160, nullable: false)]
    private string $actionClass;

    public function getAction(): ActionInterface
    {
        return $this->action;
    }

    public function getActionClass(): string
    {
        return $this->actionClass;
    }

    public function setAction(ActionInterface $action): self
    {
        $this->action = $action;
        $this->actionClass = get_class($action);

        return $this;
    }

    #[ORM\Column(name: 'entity_class', type: 'string', length: 160, nullable: false)]
    private string $entityClass;

    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }

    public function setEntity(EntityInterface $entity): self
    {
        $this->entity = $entity;
        $this->entityClass = get_class($entity);

        return $this;
    }

    #[ORM\Column(name: 'context', type: 'json', nullable: false, options: ['jsonb' => true])]
    private ContextInterface $context;

    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    public function setContext(ContextInterface $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getActor(): ?ActorInterface
    {
        return $this->actor ?? null;
    }

    public function setActor(?ActorInterface $actor): self
    {
        $this->actor = $actor;

        return $this;
    }

    #[ORM\Column(name: 'started_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $startedAt = null;

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt ?? null;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    #[ORM\Column(name: 'finished_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $finishedAt = null;

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt ?? null;
    }

    public function setFinishedAt(?\DateTimeInterface $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    #[ORM\Column(name: 'action_result', type: 'string', enumType: ActionResult::class, nullable: false)]
    private ActionResult $result;

    public function getResult(): ActionResult
    {
        return $this->result;
    }

    public function setResult(ActionResult $result): self
    {
        $this->result = $result;

        return $this;
    }

    #[ORM\Column(name: 'reason', type: 'string', length: 500, nullable: true)]
    private ?string $reason = null;

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }
}
