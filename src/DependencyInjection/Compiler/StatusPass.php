<?php

namespace SprintF\Bundle\Wolfflow\DependencyInjection\Compiler;

use SprintF\Bundle\Wolfflow\Status\StatusAbstract;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class StatusPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('workflow.status') as $id => $tags) {
            $definition = $container->getDefinition($id);

            // Проверяем, что класс действительно наследует StatusAbstract
            $class = $container->getParameterBag()->resolveValue($definition->getClass());
            if (is_subclass_of($class, StatusAbstract::class)) {
                // Добавляем вызов метода setTranslator во все такие сервисы
                $definition->addMethodCall('setTranslator', [new Reference('translator')]);
            }
        }
    }
}
