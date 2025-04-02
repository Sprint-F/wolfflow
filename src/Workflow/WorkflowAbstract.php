<?php

namespace SprintF\Bundle\Wolfflow\Workflow;

use SprintF\Bundle\Wolfflow\Action\ActionCollection;
use SprintF\Bundle\Wolfflow\Attribute\AsWorkflow;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;
use SprintF\Bundle\Wolfflow\Status\StatusCollection;
use Symfony\Contracts\Service\Attribute\Required;

abstract class WorkflowAbstract implements WorkflowInterface
{
    final protected static function getDefaultName(): string
    {
        $asWorkflowAttributes = (new \ReflectionClass(static::class))->getAttributes(AsWorkflow::class);

        return !empty($asWorkflowAttributes) ? $asWorkflowAttributes[0]->newInstance()->name : throw new NoNeededAttributeException();
    }

    public static function getName(): string
    {
        return static::getDefaultName();
    }

    protected readonly array $actions;

    /**
     *  К сожалению, атрибут здесь не несет никакой функции и указан лишь для наглядности...
     */
    #[Required]
    public function setActions(ActionCollection $actions): void
    {
        $this->actions = $actions->getAllByWorkflow(static::getName());
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    protected readonly array $statuses;

    /**
     *  К сожалению, атрибут здесь не несет никакой функции и указан лишь для наглядности...
     */
    #[Required]
    public function setStatuses(StatusCollection $statuses): void
    {
        $this->statuses = $statuses->getAllByWorkflow(static::getName());
    }

    public function getStatuses(): array
    {
        return $this->statuses;
    }
}
