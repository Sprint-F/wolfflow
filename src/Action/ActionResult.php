<?php

namespace SprintF\Bundle\Wolfflow\Action;

/**
 * Результат выполнения действия бизнес-процесса.
 */
enum ActionResult
{
    case CANNOT;
    case PROGRESS;
    case SUCCESS;
    case FAIL;
}
