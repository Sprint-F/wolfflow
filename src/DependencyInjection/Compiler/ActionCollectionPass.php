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
     * получать коллекции действий бизнес-процессов.
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ActionCollection::class)) {
            return;
        }

        /*
         * Работаем с сервисами бизнес-процессов.
         * В каждый из них прокидываем ссылку на объект-коллекцию действий.
         */
        foreach ($container->findTaggedServiceIds('workflow.workflow') as $id => $tags) {
            $container->findDefinition($id)->addMethodCall('setActions', [
                new Reference(ActionCollection::class),
            ]);
        }

        /*
         * Работаем с сервисами действий бизнес-процессов.
         * Передаем данные о них в объект-коллекцию действий, указывая, к какому бизнес-процессу относится действие.
         */
        /** @var array $taggedServices Список всех сервисов с тегом workflow.action - это все действия всех бизнес-процессов */
        $taggedServices = $container->findTaggedServiceIds('workflow.action');

        $definition = $container->findDefinition(ActionCollection::class);
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
