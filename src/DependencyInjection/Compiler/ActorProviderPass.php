<?php

namespace SprintF\Bundle\Wolfflow\DependencyInjection\Compiler;

use SprintF\Bundle\Wolfflow\Actor\ActorProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Смысл этого - в автоматической подстановке объекта типа ActorInterface в сервисы действий бизнес-процессов.
 */
class ActorProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has('security.helper')) {
            $definition = $container->findDefinition(ActorProvider::class);
            $definition->addArgument(new Reference('security.helper', ContainerInterface::NULL_ON_INVALID_REFERENCE));
        }
    }
}
