<?php

namespace SprintF\Bundle\Wolfflow\DependencyInjection\Compiler;

use SprintF\Bundle\Wolfflow\Action\ActionCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ActionCollectionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        if (!$containerBuilder->has(ActionCollection::class)) {
            return;
        }

        $definition = $containerBuilder->findDefinition(ActionCollection::class);

        $taggedServices = $containerBuilder->findTaggedServiceIds('workflow.action');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addActionToWorflow', [
                    new Reference($id),
                    $attributes['workflow'],
                ]);
            }
        }
    }
}
