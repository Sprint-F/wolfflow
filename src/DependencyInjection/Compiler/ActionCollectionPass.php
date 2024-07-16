<?php

namespace SprintF\Bundle\Wolfflow\DependencyInjection\Compiler;

use SprintF\Bundle\Wolfflow\Action\ActionCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ActionCollectionPass implements CompilerPassInterface
{
    /**
     * Всё это сделано, чтобы иметь возможность в сервисе ActionCollection
     * получить сервисы действий бизнес-процессов, сгруппированными по бизнес-процессам.
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ActionCollection::class)) {
            return;
        }

        $definition = $container->findDefinition(ActionCollection::class);

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
