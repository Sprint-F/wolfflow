<?php

namespace SprintF\Bundle\Wolfflow\Action;

use SprintF\Bundle\Wolfflow\Attribute\AsAction;
use SprintF\Bundle\Wolfflow\Context\ContextInterface;
use SprintF\Bundle\Wolfflow\Entity\WorkflowEntityInterface;
use SprintF\Bundle\Wolfflow\Exception\CanNotException;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowCollection;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowInterface;
use Symfony\Contracts\Service\Attribute\Required;

use function Symfony\Component\Translation\t;

/**
 * Абстрактный класс действия бизнес-процесса.
 *
 * Может использоваться, как базовый класс для действий в приложении, использующем Wolfflow.
 */
abstract class ActionAbstract implements ActionInterface
{
    /**
     * Используется в Dependency Injection. Именно поэтому public.
     * К сожалению, атрибут здесь не несет никакой функции и указан лишь для наглядности...
     */
    #[Required]
    public WorkflowCollection $workflows;

    /**
     * Сущность, над которой будет производиться дейтсвие.
     */
    protected readonly WorkflowEntityInterface $entity;

    /**
     * Контекст действия.
     */
    protected readonly ContextInterface $context;

    public function getDefaultWorkflowName(): string
    {
        $asActionAttributes = (new \ReflectionClass(static::class))->getAttributes(AsAction::class);

        return !empty($asActionAttributes) ? $asActionAttributes[0]->newInstance()->workflow : throw new NoNeededAttributeException();
    }

    public function getWorkflow(): WorkflowInterface
    {
        return $this->workflows->findByName($this::getDefaultWorkflowName());
    }

    /**
     * @inheritdoc
     * @throws CanNotException
     */
    public function setEntity(WorkflowEntityInterface $entity): static
    {
        if ($this->getWorkflow() !== $this->workflows->findByEntity($entity)) {
            throw new CanNotException(t('workflow.entity.isnotsameasaction'));
        }

        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEntity(): WorkflowEntityInterface
    {
        return $this->entity;
    }

    /**
     * @inheritdoc
     */
    public function setContext(ContextInterface $context): static
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    /**
     * @inheritdoc
     */
    public function can(): bool
    {
        return true;
    }
}
