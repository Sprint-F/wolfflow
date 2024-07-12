<?php

namespace SprintF\Bundle\Wolfflow\Action;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class ActionCollection
{
    public function __construct(
        #[TaggedIterator('wolfflow.action')]
        private readonly iterable $actions
    ) {
    }

    public function all(): iterable
    {
        return $this->actions;
    }
}
