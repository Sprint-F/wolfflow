<?php

namespace SprintF\Bundle\Wolfflow\Action;

use SprintF\Bundle\Wolfflow\Actor\ActorInterface;
use SprintF\Bundle\Wolfflow\Context\ContextInterface;
use SprintF\Bundle\Wolfflow\Entity\WorkflowEntityInterface;
use SprintF\Bundle\Wolfflow\Exception\CanNotException;
use SprintF\Bundle\Wolfflow\Exception\FailException;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowInterface;

/**
 * Общий интерфейс для всех действий всех бизнес-процессов.
 */
interface ActionInterface
{
    /**
     * Метод, возвращающий имя бизнес-процесса, которому принадлежит данное действие.
     * По умолчанию метод должен возвращать значение свойства атрибута AsAction::workflow.
     */
    public function getDefaultWorkflowName(): string;

    /**
     * Метод, возвращающий объект бизнес-процесса, которому принадлежит данное действие.
     */
    public function getWorkflow(): WorkflowInterface;

    /**
     * Сущность, над которой будет производиться действие.
     */
    public function setEntity(WorkflowEntityInterface $entity);

    /**
     * Сущность, над которой будет производиться действие.
     */
    public function getEntity(): WorkflowEntityInterface;

    /**
     * Контекст данного действия.
     */
    public function setContext(ContextInterface $context);

    /**
     * Контекст данного действия.
     */
    public function getContext(): ContextInterface;

    /**
     * Актор, то есть тот, кто производит данное действие.
     * Чаще всего это будет текущий авторизованный пользователь приложения.
     */
    public function setActor(?ActorInterface $actor);

    /**
     * Актор, то есть тот, кто производит данное действие.
     * Чаще всего это будет текущий авторизованный пользователь приложения.
     */
    public function getActor(): ?ActorInterface;

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
