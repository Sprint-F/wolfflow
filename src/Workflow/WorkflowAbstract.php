<?php

namespace SprintF\Bundle\Wolfflow\Workflow;

use SprintF\Bundle\Wolfflow\Action\ActionCollection;
use SprintF\Bundle\Wolfflow\Attribute\AsWorkflow;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;
use Symfony\Contracts\Service\Attribute\Required;

abstract class WorkflowAbstract implements WorkflowInterface
{
    protected readonly ActionCollection $actions;

    // #[Required]
    public function setActions(ActionCollection $actions): void
    {
        $this->actions = $actions;
    }

    public function getActions(): array
    {
        return $this->actions->allByWorkflow(static::getDefaultName());
    }

    public static function getDefaultName(): string
    {
        $asWorkflowAttributes = (new \ReflectionClass(static::class))->getAttributes(AsWorkflow::class);

        return !empty($asWorkflowAttributes) ? $asWorkflowAttributes[0]->newInstance()->name : throw new NoNeededAttributeException();
    }
}
