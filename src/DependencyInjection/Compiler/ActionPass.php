<?php

namespace SprintF\Bundle\Wolfflow\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use SprintF\Bundle\Wolfflow\Action\ActionAbstract;

class ActionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('workflow.action') as $id => $tags) {
            $definition = $container->getDefinition($id);

            // Проверяем, что класс действительно наследует ActionAbstract
            $class = $container->getParameterBag()->resolveValue($definition->getClass());
            if (is_subclass_of($class, ActionAbstract::class)) {
                // Добавляем вызов метода setTranslator во все такие сервисы
                $definition->addMethodCall('setTranslator', [new Reference('translator')]);
            }
        }
    }
}
