<?php

namespace SprintF\Bundle\Wolfflow\Tests\Unit\Status;

use SprintF\Bundle\Wolfflow\Attribute\AsStatus;
use SprintF\Bundle\Wolfflow\Attribute\AsWorkflow;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;
use SprintF\Bundle\Wolfflow\Status\StatusAbstract;
use SprintF\Bundle\Wolfflow\Status\StatusCollection;
use SprintF\Bundle\Wolfflow\Tests\Support\UnitTester;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowAbstract;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowCollection;

#[AsWorkflow(name: 'foo')]
class TestWorkflowFooInStatusAbstractTest extends WorkflowAbstract
{
}

#[AsStatus(workflow: 'foo', name: 'Test Status')]
class TestStatus extends StatusAbstract
{
    public function __invoke(): bool
    {
        return true;
    }
}

#[AsStatus(workflow: 'foo')]
class TestStatusWithEmptyName extends StatusAbstract
{
    public function __invoke(): bool
    {
        return true;
    }
}
class StatusAbstractTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    protected StatusCollection $allStatuses;

    protected function _before()
    {
        $allWorkflows = [
            new TestWorkflowFooInStatusAbstractTest(),
        ];

        $this->allWorkflows = new WorkflowCollection($allWorkflows);
        foreach ($allWorkflows as $workflow) {
            $this->allWorkflows->addWorkflowByName($workflow, $workflow->getName());
        }
    }

    // tests
    public function testNoNeededAttribute()
    {
        $status = new class extends StatusAbstract {
            public function __invoke(): bool
            {
                return true;
            }
        };

        $this->expectException(NoNeededAttributeException::class);
        $wfName = $status->getWorkflowName();
    }

    public function testDefaultWorkflowName()
    {
        $status = new TestStatus();

        $this->assertSame('Test Status', $status->getName());
    }

    public function testEmptyDefaultName()
    {
        $status = new TestStatusWithEmptyName();

        $this->assertNull($status->getName());
    }

    public function testDefaultName()
    {
        $status = new TestStatus();

        $this->assertSame('foo', $status->getWorkflowName());
    }

}
