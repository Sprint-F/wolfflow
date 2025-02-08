<?php

namespace SprintF\Bundle\Wolfflow\Actor;

use Symfony\Bundle\SecurityBundle\Security;

/**
 * Провайдер, возвращающий объект стандартного актора действий:
 * по умолчанию это текущий пользователь приложения.
 */
class DefaultActorProvider implements ActorProviderInterface
{
    public function __construct(
        private readonly ?Security $security,
    ) {
    }

    public function getActor(): ?ActorInterface
    {
        return $this->security?->getUser();
    }
}
