<?php

namespace SprintF\Bundle\Wolfflow\Tests\Unit\Action;

use SprintF\Bundle\Wolfflow\Action\ActionAbstract;
use SprintF\Bundle\Wolfflow\Action\ActionLogEntryNullTrait;
use SprintF\Bundle\Wolfflow\Attribute\AsAction;
use SprintF\Bundle\Wolfflow\Attribute\AsWorkflow;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;
use SprintF\Bundle\Wolfflow\Tests\Support\UnitTester;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowAbstract;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowCollection;

#[AsWorkflow(name: 'foo')]
class TestWorkflowFooInActionAbstractTest extends WorkflowAbstract
{
}

#[AsAction(workflow: 'foo')]
class TestAction extends ActionAbstract
{
    use ActionLogEntryNullTrait;

    public function do()
    {
    }
}
class ActionAbstractTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    protected WorkflowCollection $allWorkflows;

    protected function _before()
    {
        $allWorkflows = [
            new TestWorkflowFooInActionAbstractTest(),
        ];

        $this->allWorkflows = new WorkflowCollection($allWorkflows);
        foreach ($allWorkflows as $workflow) {
            $this->allWorkflows->addWorkflowByName($workflow, $workflow->getName());
        }
    }

    // tests
    public function testNoNeededAttribute()
    {
        $action = new class extends ActionAbstract {
            use ActionLogEntryNullTrait;

            public function do()
            {
            }
        };

        $this->expectException(NoNeededAttributeException::class);
        $wfName = $action->getWorkflowName();
    }

    public function testDefaultWorkflowName()
    {
        $action = new TestAction();

        $this->assertSame('foo', $action->getWorkflowName());
    }

    public function testWorkflow()
    {
        $action = new TestAction();
        $action->workflows = $this->allWorkflows;

        $this->assertSame($this->allWorkflows->all()[0], $action->getWorkflow());
    }

    public function testSetEntity()
    {
    }
}
