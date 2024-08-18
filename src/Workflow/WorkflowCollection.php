<?php

namespace SprintF\Bundle\Wolfflow\Workflow;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

/**
 * Объект-коллекция бизнес-процессов.
 */
class WorkflowCollection
{
    private array $workflowsByName = [];

    public function __construct(
        #[TaggedIterator('workflow.workflow')]
        private readonly iterable $allWorkflows,
    ) {
    }

    /**
     * Служебный метод, выполняется во время компиляции, чтобы сгруппировать бизнес-процессы по символическому имени.
     */
    final public function addWorkflowByName(WorkflowInterface $wf, string $name): void
    {
        $this->workflowsByName[$name] = $wf;
    }

    /**
     * Метод получения списка всех бизнес-процессов.
     *
     * @return array|WorkflowInterface[]
     */
    public function all(): array
    {
        return iterator_to_array($this->allWorkflows);
    }

    /**
     * Метод получения объекта бизнес-процесса по его символическому имени.
     */
    public function findByName(string $name): ?WorkflowInterface
    {
        return $this->workflowsByName[$name] ?? null;
    }
}
