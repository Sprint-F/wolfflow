<?php

namespace SprintF\Bundle\Wolfflow\Action;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class ActionCollection
{
    private array $actionsByWorkflow;

    public function __construct(
        #[TaggedIterator('workflow.action')]
        private readonly iterable $allActions,
    ) {
    }

    public function all(): iterable
    {
        return $this->allActions;
    }

    /**
     * @todo ActionInterface
     */
    final public function addActionToWorflow(object $action, string $workflow): self
    {
        $this->actionsByWorkflow[$workflow][] = $action;

        return $this;
    }

    final public function allByWorkflow(string $workflow): array
    {
        return $this->actionsByWorkflow[$workflow] ?? [];
    }
}
