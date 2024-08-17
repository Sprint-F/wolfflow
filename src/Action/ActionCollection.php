<?php

namespace SprintF\Bundle\Wolfflow\Action;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

/**
 * Сервис, позволяющий работать с коллекциями действий бизнес-процессов.
 */
class ActionCollection
{
    private array $actionsByWorkflow = [];

    public function __construct(
        #[TaggedIterator('workflow.action')]
        private readonly iterable $allActions,
    ) {
    }

    /**
     * Все действия всех бизнес-процессов.
     */
    public function all(): iterable
    {
        return $this->allActions;
    }

    final public function addActionToWorkflow(ActionInterface $action, string $workflow): void
    {
        $this->actionsByWorkflow[$workflow][] = $action;
    }

    /**
     * Все действия всех бизнес-процессов, сгруппированные по процессам
     */
    public function allByWorkflow(string $workflow): array
    {
        return $this->actionsByWorkflow[$workflow] ?? [];
    }
}
