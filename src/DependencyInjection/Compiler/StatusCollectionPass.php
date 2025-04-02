<?php

namespace SprintF\Bundle\Wolfflow\DependencyInjection\Compiler;

use SprintF\Bundle\Wolfflow\Status\StatusCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class StatusCollectionPass implements CompilerPassInterface
{
    /**
     * Всё это сделано, чтобы иметь возможность в разных сервисах получать коллекции статусов бизнес-процессов.
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(StatusCollection::class)) {
            return;
        }

        /*
         * Работаем с сервисами бизнес-процессов.
         * В каждый из них прокидываем ссылку на объект-коллекцию статусов.
         */
        foreach ($container->findTaggedServiceIds('workflow.workflow') as $id => $tags) {
            $container->findDefinition($id)->addMethodCall('setStatuses', [
                new Reference(StatusCollection::class),
            ]);
        }

        /*
         * Работаем с сервисами статусов бизнес-процессов.
         * Передаем данные о них в объект-коллекцию статусов, указывая, к какому бизнес-процессу относится статус.
         */
        $statusCollectionDefinition = $container->findDefinition(StatusCollection::class);
        foreach ($container->findTaggedServiceIds('workflow.status') as $id => $tags) {
            foreach ($tags as $attributes) {
                // Это очень странное место. Возможно в компиляторе Symfony есть ошибки.
                // Но сюда мы попадали с сервисами, которые не являются нашими статусами.
                // @todo: разобраться, почему нам тут нужен isset !
                if (isset($attributes['workflow'])) {
                    $statusCollectionDefinition->addMethodCall('addStatusToWorkflow', [
                        new Reference($id),
                        $attributes['workflow'],
                    ]);
                }
            }
        }
    }
}
