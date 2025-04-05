<?php

namespace SprintF\Bundle\Wolfflow\Status;

use SprintF\Bundle\Wolfflow\Action\ActionInterface;
use SprintF\Bundle\Wolfflow\Action\ActionResult;
use SprintF\Bundle\Wolfflow\Actor\ActorInterface;
use SprintF\Bundle\Wolfflow\LogEntry\LogEntryInterface;

/**
 * Набор полезных методов для работы с action log, в случае, если он реализован с помощью Doctrine.
 *
 * @mixin StatusAbstract
 */
trait StatusDoctrineTrait
{
    /**
     * Проверка: была ли ранее хотя бы одна попытка совершить такое действие?
     *
     * @param class-string<ActionInterface> $actionClass
     */
    protected function logContainsActionAttempt(string $actionClass, ?ActorInterface $actor = null): bool
    {
        return $this->getEntity()->getLogEntries()->exists(function ($key, LogEntryInterface $logEntry) use ($actionClass, $actor) {
            if (null === $actor) {
                return $actionClass === $logEntry->getActionClass();
            } else {
                return $actionClass === $logEntry->getActionClass() && $actor === $logEntry->getActor();
            }
        });
    }

    /**
     * Проверка: было ли ранее хотя бы раз успешно совершено такое действие?
     *
     * @param class-string<ActionInterface> $actionClass
     */
    protected function logContainsActionSuccess(string $actionClass, ?ActorInterface $actor = null): bool
    {
        return $this->getEntity()->getLogEntries()->exists(function ($key, LogEntryInterface $logEntry) use ($actionClass, $actor) {
            if (null === $actor) {
                return $actionClass === $logEntry->getActionClass() && ActionResult::SUCCESS === $logEntry->getResult();
            } else {
                return $actionClass === $logEntry->getActionClass() && ActionResult::SUCCESS === $logEntry->getResult() && $actor === $logEntry->getActor();
            }
        });
    }

    /**
     * Проверка: было ли ранее хотя бы раз неуспешно совершено такое действие?
     *
     * @param class-string<ActionInterface> $actionClass
     */
    protected function logContainsActionFail(string $actionClass, ?ActorInterface $actor = null): bool
    {
        return $this->getEntity()->getLogEntries()->exists(function ($key, LogEntryInterface $logEntry) use ($actionClass, $actor) {
            if (null === $actor) {
                return $actionClass === $logEntry->getActionClass() && ActionResult::FAIL === $logEntry->getResult();
            } else {
                return $actionClass === $logEntry->getActionClass() && ActionResult::FAIL === $logEntry->getResult() && $actor === $logEntry->getActor();
            }
        });
    }

    /**
     * Проверка: была ли успешной последняя попытка совершения такого действия?
     *
     * @param class-string<ActionInterface> $actionClass
     */
    protected function wasLastActionAttemptSuccess(string $actionClass, ?ActorInterface $actor = null): bool
    {
        $actionLogEntries = $this->getEntity()->getLogEntries()->filter(function (LogEntryInterface $logEntry) use ($actionClass, $actor) {
            if (null === $actor) {
                return $actionClass === $logEntry->getActionClass();
            } else {
                return $actionClass === $logEntry->getActionClass() && $actor === $logEntry->getActor();
            }
        })->toArray();
        if (empty($actionLogEntries)) {
            return false;
        }

        usort($actionLogEntries, function (LogEntryInterface $logEntry1, LogEntryInterface $logEntry2) {
            return $logEntry1->getFinishedAt() <=> $logEntry2->getFinishedAt();
        });

        return ActionResult::SUCCESS === end($actionLogEntries)->getResult();
    }

    /**
     * Проверка: была ли неуспешной последняя попытка совершения такого действия?
     *
     * @param class-string<ActionInterface> $actionClass
     */
    protected function wasLastActionAttemptFail(string $actionClass, ?ActorInterface $actor = null): bool
    {
        $actionLogEntries = $this->getEntity()->getLogEntries()->filter(function (LogEntryInterface $logEntry) use ($actionClass, $actor) {
            if (null === $actor) {
                return $actionClass === $logEntry->getActionClass();
            } else {
                return $actionClass === $logEntry->getActionClass() && $actor === $logEntry->getActor();
            }
        })->toArray();
        if (empty($actionLogEntries)) {
            return false;
        }

        usort($actionLogEntries, function (LogEntryInterface $logEntry1, LogEntryInterface $logEntry2) {
            return $logEntry1->getFinishedAt() <=> $logEntry2->getFinishedAt();
        });

        return ActionResult::FAIL === end($actionLogEntries)->getResult();
    }
}
