<?php

namespace Tests {

    use Brexis\LaravelWorkflow\Commands\WorkflowDumpCommand;
    use Mockery;
    use PHPUnit\Framework\TestCase;

    class WorkflowDumpCommandTest extends TestCase
    {
        public function testShouldThrowExceptionForUndefinedWorkflow()
        {
            $command = Mockery::mock(WorkflowDumpCommand::class)
                              ->makePartial()
                              ->shouldReceive('argument')
                              ->with('workflow')
                              ->andReturn('fake')
                              ->shouldReceive('option')
                              ->with('format')
                              ->andReturn('png')
                              ->shouldReceive('option')
                              ->with('class')
                              ->andReturn('Tests\Fixtures\TestObject')
                              ->getMock();

            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('Workflow fake is not configured.');
            $command->handle(new \WorkflowMock());
        }

        public function testShouldThrowExceptionForUndefinedClass()
        {
            $command = Mockery::mock(WorkflowDumpCommand::class)
                              ->makePartial()
                              ->shouldReceive('argument')
                              ->with('workflow')
                              ->andReturn('straight')
                              ->shouldReceive('option')
                              ->with('format')
                              ->andReturn('png')
                              ->shouldReceive('option')
                              ->with('class')
                              ->andReturn('Tests\Fixtures\FakeObject')
                              ->getMock();

            $this->expectException(\Exception::class);
            $this->expectExceptionMessage(
                'Workflow straight has no support for' .
                ' class Tests\Fixtures\FakeObject. Please specify a valid support' .
                ' class with the --class option.'
            );
            $command->handle(new \WorkflowMock());
        }

        public function testWorkflowCommand()
        {
            if (file_exists('straight.png')) {
                unlink('straight.png');
            }

            $command = Mockery::mock(WorkflowDumpCommand::class)
                              ->makePartial()
                              ->shouldReceive('argument')
                              ->with('workflow')
                              ->andReturn('straight')
                              ->shouldReceive('option')
                              ->with('format')
                              ->andReturn('png')
                              ->shouldReceive('option')
                              ->with('class')
                              ->andReturn('Tests\Fixtures\TestObject')
                              ->getMock();

            $command->handle(new \WorkflowMock());

            $this->assertTrue(file_exists('straight.png'));
        }
    }
}

namespace {

    use Brexis\LaravelWorkflow\WorkflowLibrarian;
    use Brexis\LaravelWorkflow\WorkflowLibrarianInterface;
    use Symfony\Component\Workflow\Workflow;

    $config = [
        'straight' => [
            'supports'    => ['Tests\Fixtures\TestObject'],
            'places'      => ['a', 'b', 'c'],
            'transitions' => [
                't1' => [
                    'from' => 'a',
                    'to'   => 'b',
                ],
                't2' => [
                    'from' => 'b',
                    'to'   => 'c',
                ],
            ],
        ],
    ];

    class WorkflowMock implements WorkflowLibrarianInterface
    {
        private $librarian;

        public function __construct()
        {
            global $config;

            $this->librarian = new WorkflowLibrarian($config);
        }

        public function get($subject, ?string $workflowName = null): Workflow
        {
            return $this->librarian->get($subject, $workflowName);
        }

        public function add(Workflow $workflow, string $supportStrategy): void
        {
            $this->librarian->add($workflow, $supportStrategy);
        }

        public function addFromArray(string $name, array $workflowData): void
        {
            $this->librarian->addFromArray($name, $workflowData);
        }
    }

    class Config
    {
        public static function get($name)
        {
            global $config;

            return $config;
        }
    }
}
