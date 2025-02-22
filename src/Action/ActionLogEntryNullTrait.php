<?php

namespace SprintF\Bundle\Wolfflow\Action;

use SprintF\Bundle\Wolfflow\LogEntry\LogEntryInterface;

/**
 * Реализация методов Action::insertLogEntry() и Action::updateLogEntry() для тестов.
 */
trait ActionLogEntryNullTrait
{
    protected function insertLogEntry(LogEntryInterface $logEntry): LogEntryInterface
    {
        return $logEntry;
    }

    protected function updateLogEntry(LogEntryInterface $logEntry): LogEntryInterface
    {
        return $logEntry;
    }
}
