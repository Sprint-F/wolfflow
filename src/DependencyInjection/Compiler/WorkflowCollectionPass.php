<?php

namespace SprintF\Bundle\Wolfflow\DependencyInjection\Compiler;

use SprintF\Bundle\Wolfflow\Workflow\WorkflowCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WorkflowCollectionPass implements CompilerPassInterface
{
    /**
     * Всё это сделано, чтобы иметь возможность в разных сервисах
     * получать колллекции бизнес-процессов.
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(WorkflowCollection::class)) {
            return;
        }

        /*
         * Работаем с сервисами бизнес-процессов.
         * Передаем данные о них в объект-коллекцию бизнес-процессов, указывая для каждого символическое имя.
         */
        /** @var array $taggedServices Список всех сервисов с тегом workflow.workflow - это все бизнес-процессы */
        $taggedServices = $container->findTaggedServiceIds('workflow.workflow');

        $definition = $container->findDefinition(WorkflowCollection::class);
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addWorkflowByName', [
                    new Reference($id),
                    $attributes['name'],
                ]);
            }
        }
    }
}
