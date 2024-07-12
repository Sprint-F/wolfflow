<?php

namespace SprintF\Bundle\Wolfflow;

use SprintF\Bundle\Wolfflow\Attribute\AsAction;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SprintFWolfflowBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        /* Добавляем тег workflow.action ко всем сервисам, помеченным атрибутом #[AsAction] */
        $builder->registerAttributeForAutoconfiguration(AsAction::class, static function (ChildDefinition $definition, AsAction $attribute, \ReflectionClass $reflector): void {
            $definition->addTag('workflow.action', ['workflow' => $attribute->workflow]);
        });
    }
}
