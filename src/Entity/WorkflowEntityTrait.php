<?php

namespace SprintF\Bundle\Wolfflow\Entity;

// use SprintF\Bundle\Workflow\ActionLog\ActionLogEntryInterface;
use SprintF\Bundle\Wolfflow\Attribute\AsWorkflowEntity;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;

/**
 * Заготовка кода для сущностей бизнес-процессов.
 *
 * @phpstan-require-implements WorkflowEntityInterface
 */
trait WorkflowEntityTrait // implements WorkflowEntityInterface
{
    public function getDefaultWorkflowName(): string
    {
        $asWorkflowEntityAttributes = (new \ReflectionClass(static::class))->getAttributes(AsWorkflowEntity::class);

        return !empty($asWorkflowEntityAttributes) ? $asWorkflowEntityAttributes[0]->newInstance()->workflow : throw new NoNeededAttributeException();
    }

    /**
     * @return class-string<WorkflowEntityInterface>
     */
    public function getEntityClass(): string
    {
        return get_class($this);
    }

    public function getEntityId(): int|string|null
    {
        return $this->id;
    }

    public function setEntityId(int|string|null $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function isNew(): bool
    {
        return !empty($this->id);
    }
}
