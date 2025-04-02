<?php

namespace SprintF\Bundle\Wolfflow\Status;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

/**
 * Объект-коллекция статусов бизнес-процессов.
 */
class StatusCollection
{
    private array $statusesByWorkflow = [];

    public function __construct(
        #[TaggedIterator('workflow.status')]
        private readonly iterable $allStatuses,
    ) {
    }

    /**
     * Служебный метод, выполняется во время компиляции, чтобы сгруппировать статусы по бизнес-процессам.
     */
    final public function addStatusToWorkflow(StatusInterface $status, string $workflow): void
    {
        $this->statusesByWorkflow[$workflow][] = $status;
    }

    /**
     * Метод получения списка всех действий всех бизнес-процессов.
     *
     * @return array|StatusInterface[]
     */
    public function all(): array
    {
        return iterator_to_array($this->allStatuses);
    }

    /**
     * Метод получения списка всех статусов конкретного бизнес-процесса по его символическому имени.
     *
     * @return array|StatusInterface[]
     */
    public function getAllByWorkflow(string $workflow): array
    {
        return $this->statusesByWorkflow[$workflow] ?? [];
    }
}
