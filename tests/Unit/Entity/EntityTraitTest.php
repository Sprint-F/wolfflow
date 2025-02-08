<?php

namespace Tests\Unit\Entity;

use SprintF\Bundle\Wolfflow\Attribute\AsEntity;
use SprintF\Bundle\Wolfflow\Attribute\AsWorkflow;
use SprintF\Bundle\Wolfflow\Entity\EntityInterface;
use SprintF\Bundle\Wolfflow\Entity\EntityTrait;
use SprintF\Bundle\Wolfflow\Exception\NoNeededAttributeException;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowAbstract;
use SprintF\Bundle\Wolfflow\Workflow\WorkflowCollection;
use Tests\Support\UnitTester;

#[AsWorkflow(name: 'foo')]
class TestWorkflowFooInEntityTraitTest extends WorkflowAbstract
{
}

#[AsEntity(workflow: 'foo', logEntryClass: '')]
class TestEntity implements EntityInterface
{
    use EntityTrait;
    private $id;

    public function getLogEntries(): iterable
    {
        return [];
    }
}

class EntityTraitTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    protected WorkflowCollection $allWorkflows;

    protected function _before()
    {
        $allWorkflows = [
            new TestWorkflowFooInEntityTraitTest(),
        ];

        $this->allWorkflows = new WorkflowCollection($allWorkflows);
        foreach ($allWorkflows as $workflow) {
            $this->allWorkflows->addWorkflowByName($workflow, $workflow->getName());
        }
    }

    // tests
    public function testNoNeededAttribute()
    {
        $entity = new class implements EntityInterface {
            use EntityTrait;

            public function getLogEntries(): iterable
            {
                return [];
            }
        };

        $this->expectException(NoNeededAttributeException::class);
        $wfName = $entity->getWorkflowName();
    }

    public function testGetEntityClass()
    {
        $entity = new TestEntity();

        $this->assertSame(TestEntity::class, $entity->getEntityClass());
    }

    public function testDefaultWorkflowName()
    {
        $entity = new TestEntity();

        $this->assertSame('foo', $entity->getWorkflowName());
    }

    public function testSetGetIdIsNew()
    {
        $entity = new TestEntity();

        $this->assertNull($entity->getEntityId());
        $this->assertTrue($entity->isNew());

        $entity->setEntityId(42);

        $this->assertSame(42, $entity->getEntityId());
        $this->assertFalse($entity->isNew());
    }
}
