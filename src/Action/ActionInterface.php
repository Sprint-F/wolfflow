<?php

namespace SprintF\Bundle\Wolfflow\Action;

use SprintF\Bundle\Wolfflow\Context\ContextInterface;
use SprintF\Bundle\Wolfflow\Entity\WorkflowEntityInterface;
use SprintF\Bundle\Wolfflow\Exception\CanNotException;
use SprintF\Bundle\Wolfflow\Exception\FailException;

/**
 * Общий интерфейс для всех действий всех бизнес-процессов.
 */
interface ActionInterface
{
    /**
     * Метод, возвращающий имя бизнес-процесса, которому принадлежит данное действие.
     * По умолчанию метод должен возвращать значение свойства атрибута AsAction::workflow.
     */
    public static function getDefaultWorkflowName(): string;

    /**
     * Объект бизнес-процесса, которому принадлежит действие.
     */
    public static function getWorkflow()/* :WorkflowInterface */;

    /**
     * Сущность, над которой будет производиться действие.
     */
    public function setEntity(WorkflowEntityInterface $entity);

    /**
     * Контекст данного действия.
     */
    public function setContext(ContextInterface $context);

    /**
     * Метод, определяющий, может ли быть выполнено данное действие с данной сущностью и в данном контексте.
     *
     * @throws CanNotException
     */
    public function can(): bool;

    /**
     * Непосредственно метод выполнения действия.
     *
     * @throws CanNotException
     * @throws FailException
     */
    public function __invoke(): ActionResult;
}
