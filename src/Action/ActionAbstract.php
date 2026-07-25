<?php

namespace SprintF\Bundle\Wolfflow\Action;

use SprintF\Bundle\Wolfflow\Actor\ActorInterface;
use SprintF\Bundle\Wolfflow\Actor\ActorProviderInterface;
use SprintF\Bundle\Wolfflow\Attribute\AsAction;
use SprintF\Bundle\Wolfflow\Context\ContextInterface;
use SprintF\Bundle\Wolfflow\Entity\EntityInterface;
use SprintF\Bundle\Wolfflow\Exception\CanNotException;
use SprintF\Bundle\Wolfflow\Exception\FailException;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;
use SprintF\Bundle\Wolfflow\LogEntry\LogEntryInterface;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowCollection;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    protected EntityInterface $entity;

    /**
     * Контекст действия.
     */
    protected ContextInterface $context;

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
    protected ?ActorInterface $actor;

    /**
     * Объект записи в логе данного действия.
     */
    protected LogEntryInterface $logEntry;

    /**
     * Переводчик.
     */
    protected TranslatorInterface $translator;

    #[Required]
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

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
            throw new CanNotException($this->translator->trans('workflow.entity.isnotsameasaction'));
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
        return false;
    }

    protected function start()
    {
        $logEntryClass = $this->getEntity()->getLogEntryClass();
        /** @var LogEntryInterface $logEntry */
        $logEntry = new $logEntryClass();
        $logEntry
            ->setAction($this)
            ->setEntity($this->getEntity())
            ->setContext($this->getContext())
            ->setActor($this->getActor())
            ->setStartedAt(new \DateTime('now'))
            ->setResult(ActionResult::PROGRESS)
        ;

        $logEntry = $this->insertLogEntry($logEntry);
        $this->logEntry = $logEntry;
    }

    protected function close(ActionResult $result, ?string $reason = null): void
    {
        $this->logEntry
            ->setFinishedAt(new \DateTime('now'))
            ->setResult($result)
            ->setReason($reason)
        ;

        $this->logEntry = $this->updateLogEntry($this->logEntry);
    }

    public function __invoke(?EntityInterface $entity = null, ?ContextInterface $context = null): ActionResult
    {
        if (null !== $entity) {
            $this->setEntity($entity);
        }

        if (null !== $context) {
            $this->setContext($context);
        }

        $this->start();

        try {
            $can = $this->can();
            if (!$can) {
                throw new CanNotException($this->translator->trans('action.cannot'));
            }

            $this->do();
            $this->success();

            return ActionResult::SUCCESS;
        } catch (CanNotException $e) {
            $this->cannot($e);
            throw $e;
        } catch (FailException $e) {
            $this->fail($e);
            throw $e;
        } catch (\Throwable $e) {
            $this->fail($e);
            throw new FailException(message: $e->getMessage(), previous: $e);
        }
    }

    abstract public function do();

    protected function cannot(CanNotException $exception): void
    {
        $this->close(ActionResult::CANNOT, trim(get_class($exception).': '.$exception->getMessage(), ':'));
    }

    protected function success(): void
    {
        $this->close(ActionResult::SUCCESS);
    }

    protected function fail(\Throwable $exception): void
    {
        $this->close(ActionResult::FAIL, trim(get_class($exception).': '.$exception->getMessage(), ':'));
    }

    public function getLogEntry(): LogEntryInterface
    {
        return $this->logEntry;
    }

    abstract protected function insertLogEntry(LogEntryInterface $logEntry);

    abstract protected function updateLogEntry(LogEntryInterface $logEntry);
}
