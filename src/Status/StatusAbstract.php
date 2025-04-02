<?php

namespace SprintF\Bundle\Wolfflow\Status;

use SprintF\Bundle\Wolfflow\Attribute\AsStatus;
use SprintF\Bundle\Wolfflow\Entity\EntityInterface;
use SprintF\Bundle\Wolfflow\Exception\InvalidWorkflowException;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowCollection;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowInterface;
use Symfony\Contracts\Service\Attribute\Required;

use function Symfony\Component\Translation\t;

/**
 * Абстрактный класс статуса сущности.
 *
 * Может использоваться, как базовый класс для статусов в приложении, использующем WolffloW.
 */
abstract class StatusAbstract implements StatusInterface
{
    /**
     * Используется в Dependency Injection. Именно поэтому public.
     * К сожалению, атрибут здесь не несет никакой функции и указан лишь для наглядности...
     */
    #[Required]
    public WorkflowCollection $workflows;

    /**
     * Сущность, над которой будет производиться действие.
     */
    protected readonly EntityInterface $entity;

    final protected static function getDefaultWorkflowName(): string
    {
        $asStatusAttributes = (new \ReflectionClass(static::class))->getAttributes(AsStatus::class);

        return !empty($asStatusAttributes) ? $asStatusAttributes[0]->newInstance()->workflow : throw new NoNeededAttributeException();
    }

    public static function getWorkflowName(): string
    {
        return static::getDefaultWorkflowName();
    }

    public function getWorkflow(): WorkflowInterface
    {
        return $this->workflows->findByName($this->getWorkflowName());
    }

    public function setEntity(EntityInterface $entity): static
    {
        if ($this->getWorkflow() !== $this->workflows->findByEntity($entity)) {
            throw new InvalidWorkflowException(t('workflow.entity.isnotsameasaction'));
        }

        $this->entity = $entity;

        return $this;
    }

    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }
}
