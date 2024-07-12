<?php

namespace SprintF\Bundle\Wolfflow\Actor;

/**
 * Интерфейс, задающий общие требования к "актору", то есть к тому лицу, сервису, процессу, которые производят действие.
 * В большинстве случаев актором будет пользователь приложения: добавьте этот интерфейс к сущности User.
 */
interface ActorInterface
{
    public function getId(): int|string;
}
