<?php

namespace SprintF\Bundle\Wolfflow\Action;

use SprintF\Bundle\Wolfflow\Attribute\AsAction;
use SprintF\Bundle\Wolfflow\Context\ContextInterface;
use SprintF\Bundle\Wolfflow\Entity\WorkflowEntityInterface;
use SprintF\Bundle\Wolfflow\Exception\CanNotException;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;

use function Symfony\Component\Translation\t;

/**
 * Абстрактный класс действия бизнес-процесса.
 *
 * Может использоваться, как базовый класс для действий в приложении, использующем Wolfflow.
 */
abstract class ActionAbstract implements ActionInterface
{
    protected WorkflowEntityInterface $entity;

    protected ContextInterface $context;

    public static function getDefaultWorkflowName(): string
    {
        $asActionAttributes = (new \ReflectionClass(static::class))->getAttributes(AsAction::class);

        return !empty($asActionAttributes) ? $asActionAttributes[0]->newInstance()->workflow : throw new NoNeededAttributeException();
    }

    public function setEntity(WorkflowEntityInterface $entity): static
    {
        if ($this->getWorkflow() !== $entity->getWorkflow()) {
            throw new CanNotException(t('workflow.entity.isnotsameasaction'));
        }

        $this->entity = $entity;

        return $this;
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
    public function can(): bool
    {
        return true;
    }
}
