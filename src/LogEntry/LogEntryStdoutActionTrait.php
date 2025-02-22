<?php

namespace SprintF\Bundle\Wolfflow\LogEntry;

/**
 * Реализация методов Action::insertLogEntry() и Action::updateLogEntry() для тестов.
 */
trait LogEntryStdoutActionTrait
{
    protected function insertLogEntry(LogEntryInterface $logEntry): LogEntryInterface
    {
        dump($logEntry);

        return $logEntry;
    }

    protected function updateLogEntry(LogEntryInterface $logEntry): LogEntryInterface
    {
        dump($logEntry);

        return $logEntry;
    }
}
