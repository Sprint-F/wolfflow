<?php

namespace SprintF\Bundle\Wolfflow;

use SprintF\Bundle\Wolfflow\Action\ActionInterface;
use SprintF\Bundle\Wolfflow\Attribute\AsAction;
use SprintF\Bundle\Wolfflow\Attribute\AsWorkflow;
use SprintF\Bundle\Wolfflow\DependencyInjection\Compiler\ActionCollectionPass;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowInterface;
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

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ActionCollectionPass());
        parent::build($container);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        /* Добавляем тег workflow.workflow ко всем сервисам, помеченным атрибутом #[AsWorkflow] и реализующим интерфейс WorkflowInterface */
        $builder->registerAttributeForAutoconfiguration(AsWorkflow::class, static function (ChildDefinition $definition, AsWorkflow $attribute, \ReflectionClass $reflector): void {
            if ($reflector->implementsInterface(WorkflowInterface::class)) {
                $definition->addTag('workflow.workflow', ['name' => $attribute->name]);
            }
        });

        /* Добавляем тег workflow.action ко всем сервисам, помеченным атрибутом #[AsAction] и реализующим интерфейс ActionInterface */
        $builder->registerAttributeForAutoconfiguration(AsAction::class, static function (ChildDefinition $definition, AsAction $attribute, \ReflectionClass $reflector): void {
            if ($reflector->implementsInterface(ActionInterface::class)) {
                $definition->addTag('workflow.action', ['workflow' => $attribute->workflow]);
            }
        });
    }
}
