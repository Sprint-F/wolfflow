<?php

namespace SprintF\Bundle\Wolfflow\Action;

/**
 * Тип результата выполнения действия бизнес-процесса.
 */
enum ActionResult: string
{
    case CANNOT = 'cannot';
    case PROGRESS = 'progress';
    case SUCCESS = 'success';
    case FAIL = 'fail';
}
