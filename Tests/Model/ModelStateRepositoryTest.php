<?php

namespace FreeAgent\WorkflowBundle\Tests\Model;

use FreeAgent\WorkflowBundle\Entity\ModelState;
use FreeAgent\WorkflowBundle\Tests\TestCase;

class ModelStateRepositoryTest extends TestCase
{
    private $em;

    protected function setUp()
    {
        parent::setUp();

        $this->em = $this->getMockSqliteEntityManager();
        $this->createSchema($this->em);
    }

    protected function createData()
    {
        $model1 = new ModelState();
        $model1->setWorkflowIdentifier('a1b2c3');
        $model1->setCreatedAt(new \DateTime('2012-02-12'));
        $model1->setProcessName('process_1');
        $model1->setStepName('step_A');
        $model1->setSuccessful(true);

        $this->em->persist($model1);

        $model2 = new ModelState();
        $model2->setWorkflowIdentifier('a1b2c3');
        $model2->setCreatedAt(new \DateTime('2012-02-14'));
        $model2->setProcessName('process_1');
        $model2->setStepName('step_B');
        $model2->setSuccessful(true);

        $this->em->persist($model2);

        $this->em->flush();
    }

    public function testFindLatestModelState()
    {
        $this->createData();

        $repository = $this->em->getRepository('FreeAgent\WorkflowBundle\Entity\ModelState');

        $this->assertInstanceOf('FreeAgent\WorkflowBundle\Model\ModelStateRepository', $repository);

        $this->assertNull($repository->findLatestModelState('id', 'process'));
        $this->assertNull($repository->findLatestModelState('a1b2c3', 'process_?'));

        $model = $repository->findLatestModelState('a1b2c3', 'process_1');
        $this->assertEquals('a1b2c3', $model->getWorkflowIdentifier());
        $this->assertEquals('2012-02-14', $model->getCreatedAt()->format('Y-m-d'));
        $this->assertEquals('process_1', $model->getProcessName());
        $this->assertEquals('step_B', $model->getStepName());
    }
}