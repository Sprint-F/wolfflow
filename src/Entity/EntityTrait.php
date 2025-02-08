<?php

namespace SprintF\Bundle\Wolfflow\Entity;

// use SprintF\Bundle\Workflow\ActionLog\ActionLogEntryInterface;
use SprintF\Bundle\Wolfflow\Attribute\AsEntity;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;

/**
 * Заготовка кода для сущностей бизнес-процессов.
 *
 * @phpstan-require-implements EntityInterface
 */
trait EntityTrait // implements EntityInterface
{
    final protected static function getDefaultWorkflowName(): string
    {
        $asWorkflowEntityAttributes = (new \ReflectionClass(static::class))->getAttributes(AsEntity::class);

        return !empty($asWorkflowEntityAttributes) ? $asWorkflowEntityAttributes[0]->newInstance()->workflow : throw new NoNeededAttributeException();
    }

    public static function getWorkflowName(): string
    {
        return static::getDefaultWorkflowName();
    }

    /**
     * @return class-string<EntityInterface>
     */
    public function getEntityClass(): string
    {
        return get_class($this);
    }

    public function getEntityId(): int|string|\Stringable|null
    {
        return $this->id;
    }

    public function setEntityId(int|string|\Stringable|null $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function isNew(): bool
    {
        return empty($this->id);
    }

    final protected static function getDefaultLogEntryClass(): string
    {
        $asWorkflowEntityAttributes = (new \ReflectionClass(static::class))->getAttributes(AsEntity::class);

        return !empty($asWorkflowEntityAttributes) ? $asWorkflowEntityAttributes[0]->newInstance()->logEntryClass : throw new NoNeededAttributeException();
    }

    public static function getLogEntryClass(): string
    {
        return static::getDefaultLogEntryClass();
    }
}
