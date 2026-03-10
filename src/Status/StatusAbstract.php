<?php

namespace SprintF\Bundle\Wolfflow\Status;

use SprintF\Bundle\Wolfflow\Actor\ActorInterface;
use SprintF\Bundle\Wolfflow\Attribute\AsStatus;
use SprintF\Bundle\Wolfflow\Entity\EntityInterface;
use SprintF\Bundle\Wolfflow\Exception\InvalidWorkflowException;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowCollection;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Абстрактный класс статуса сущности.
 *
 * Может использоваться, как базовый класс для статусов в приложении, использующем WolffloW.
 */
abstract class StatusAbstract implements StatusInterface
{
    /**
     * Используется в Dependency Injection. Именно поэтому public.
     * К сожалению, атрибут здесь не несет никакой функции и указан лишь для наглядности...
     */
    #[Required]
    public WorkflowCollection $workflows;

    /**
     * Сущность, статус которой мы хотим определить.
     */
    protected EntityInterface $entity;

    /**
     * Актор, для которого мы хотим определить субъективный статус.
     */
    protected ?ActorInterface $actor = null;

    /**
     * Переводчик.
     */
    protected TranslatorInterface $translator;

    #[Required]
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    final protected static function getMetaData(): array
    {
        static $attributes = [];
        $attributes[static::class] ??= (new \ReflectionClass(static::class))->getAttributes(AsStatus::class);

        return $attributes[static::class];
    }

    final protected static function getDefaultWorkflowName(): string
    {
        $asStatusAttributes = static::getMetaData();

        return !empty($asStatusAttributes) ? $asStatusAttributes[0]->newInstance()->workflow : throw new NoNeededAttributeException();
    }

    final protected static function getDefaultName(): ?string
    {
        $asStatusAttributes = static::getMetaData();

        return !empty($asStatusAttributes) ? $asStatusAttributes[0]->newInstance()->name : throw new NoNeededAttributeException();
    }

    public static function getWorkflowName(): string
    {
        return static::getDefaultWorkflowName();
    }

    public function getWorkflow(): WorkflowInterface
    {
        return $this->workflows->findByName($this->getWorkflowName());
    }

    public static function getName(): ?string
    {
        return static::getDefaultName();
    }

    public function setEntity(EntityInterface $entity): static
    {
        if ($this->getWorkflow() !== $this->workflows->findByEntity($entity)) {
            throw new InvalidWorkflowException($this->translator->trans('workflow.entity.isnotsameasaction'));
        }

        $this->entity = $entity;

        return $this;
    }

    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }

    public function setActor(?ActorInterface $actor): static
    {
        $this->actor = $actor;

        return $this;
    }

    public function getActor(): ?ActorInterface
    {
        return $this->actor;
    }
}
