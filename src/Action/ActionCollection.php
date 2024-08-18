<?php

namespace SprintF\Bundle\Wolfflow\Action;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

/**
 * Объект-коллекция действий бизнес-процессов.
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
     * Служебный метод, выполняется во время компиляции, чтобы сгруппировать действия по бизнес-процессам.
     */
    final public function addActionToWorkflow(ActionInterface $action, string $workflow): void
    {
        $this->actionsByWorkflow[$workflow][] = $action;
    }

    /**
     * Метод получения списка всех действий всех бизнес-процессов.
     *
     * @return iterable|ActionInterface[]
     */
    public function all(): iterable
    {
        return $this->allActions;
    }

    /**
     * Метод получения списка всех действий конкретного бизнес-процесса по его символическому имени.
     *
     * @return array|ActionInterface[]
     */
    public function findAllByWorkflow(string $workflow): array
    {
        return $this->actionsByWorkflow[$workflow] ?? [];
    }
}
