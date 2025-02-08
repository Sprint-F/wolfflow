<?php

namespace SprintF\Bundle\Wolfflow\Actor;

/**
 * Интерфейс, задающий требования к классу провайдера актора.
 */
interface ActorProviderInterface
{
    public function getActor(): ?ActorInterface;
}
