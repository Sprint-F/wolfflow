<?php

namespace SprintF\Bundle\Wolfflow\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineMappingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('doctrine')) {
            return;
        }

        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'WolfflowBundle' => [
                        'type' => 'attribute',
                        'dir' => __DIR__ . '/../../Entity',
                        'prefix' => 'SprintF\Bundle\Wolfflow\Entity',
                        'is_bundle' => true,
                    ]
                ]
            ],
        ]);
    }
}
