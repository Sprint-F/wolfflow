<?php

namespace SprintF\Bundle\Wolfflow\DependencyInjection\Compiler;

use SprintF\Bundle\Wolfflow\Action\ActionCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ActionCollectionPass implements CompilerPassInterface
{
    /**
     * Всё это сделано, чтобы иметь возможность в разных сервисах
     * получать колллекции сервисов действий бизнес-процессов.
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ActionCollection::class)) {
            return;
        }

        /* Работаем с сервисами бизнес-процессов */
        foreach ($container->findTaggedServiceIds('workflow.workflow') as $id => $tags) {
            $container->findDefinition($id)->addMethodCall('setActions', [
                new Reference(ActionCollection::class),
            ]);
        }

        $definition = $container->findDefinition(ActionCollection::class);

        /** @var array $taggedServices Список всех сервисов с тегом workflow.action - это все действия всех бизнес-процессов */
        $taggedServices = $container->findTaggedServiceIds('workflow.action');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addActionToWorkflow', [
                    new Reference($id),
                    $attributes['workflow'],
                ]);
            }
        }
    }
}
