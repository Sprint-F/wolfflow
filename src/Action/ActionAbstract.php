<?php

namespace SprintF\Bundle\Wolfflow\Action;

use SprintF\Bundle\Wolfflow\Actor\ActorInterface;
use SprintF\Bundle\Wolfflow\Actor\ActorProviderInterface;
use SprintF\Bundle\Wolfflow\Attribute\AsAction;
use SprintF\Bundle\Wolfflow\Context\ContextInterface;
use SprintF\Bundle\Wolfflow\Entity\EntityInterface;
use SprintF\Bundle\Wolfflow\Exception\CanNotException;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowCollection;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowInterface;
use Symfony\Contracts\Service\Attribute\Required;

use function Symfony\Component\Translation\t;

/**
 * Абстрактный класс действия бизнес-процесса.
 *
 * Может использоваться, как базовый класс для действий в приложении, использующем WolffloW.
 */
abstract class ActionAbstract implements ActionInterface
{
    /**
     * Используется в Dependency Injection. Именно поэтому public.
     * К сожалению, атрибут здесь не несет никакой функции и указан лишь для наглядности...
     */
    #[Required]
    public WorkflowCollection $workflows;

    /**
     * Сущность, над которой будет производиться действие.
     */
    protected readonly EntityInterface $entity;

    /**
     * Контекст действия.
     */
    protected readonly ContextInterface $context;

    /**
     * Используется в Dependency Injection. Именно поэтому public.
     * К сожалению, атрибут здесь не несет никакой функции и указан лишь для наглядности...
     */
    #[Required]
    public ActorProviderInterface $actorProvider;

    /**
     * Актор, то есть тот, кто производит данное действие.
     * Чаще всего это будет текущий авторизованный пользователь приложения.
     */
    protected readonly ?ActorInterface $actor;

    final protected static function getDefaultWorkflowName(): string
    {
        $asActionAttributes = (new \ReflectionClass(static::class))->getAttributes(AsAction::class);

        return !empty($asActionAttributes) ? $asActionAttributes[0]->newInstance()->workflow : throw new NoNeededAttributeException();
    }

    public static function getWorkflowName(): string
    {
        return static::getDefaultWorkflowName();
    }

    public function getWorkflow(): WorkflowInterface
    {
        return $this->workflows->findByName($this->getWorkflowName());
    }

    /**
     * @throws CanNotException
     */
    public function setEntity(EntityInterface $entity): static
    {
        if ($this->getWorkflow() !== $this->workflows->findByEntity($entity)) {
            throw new CanNotException(t('workflow.entity.isnotsameasaction'));
        }

        $this->entity = $entity;

        return $this;
    }

    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }

    public function setContext(ContextInterface $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    public function setActor(?ActorInterface $actor): static
    {
        $this->actor = $actor;

        return $this;
    }

    public function getActor(): ?ActorInterface
    {
        return $this->actor ?? $this->actorProvider->getActor();
    }

    public function can(): bool
    {
        return true;
    }
}
