<?php
namespace League\Tactician\Tests;

use League\Tactician\CommandBus;
use League\Tactician\Middleware;
use League\Tactician\Tests\Fixtures\Command\AddTaskCommand;
use Mockery;

class CommandBusTest extends \PHPUnit_Framework_TestCase
{
    public function testAllMiddlewareAreExecutedAndReturnValuesAreRespected()
    {
        $executionOrder = [];

        $middleware1 = Mockery::mock(Middleware::class);
        $middleware1->shouldReceive('execute')->andReturnUsing(
            function ($command, $next) use (&$executionOrder) {
                $executionOrder[] = 1;
                return $next($command);
            }
        );

        $middleware2 = Mockery::mock(Middleware::class);
        $middleware2->shouldReceive('execute')->andReturnUsing(
            function ($command, $next) use (&$executionOrder) {
                $executionOrder[] = 2;
                return $next($command);
            }
        );

        $middleware3 = Mockery::mock(Middleware::class);
        $middleware3->shouldReceive('execute')->andReturnUsing(
            function () use (&$executionOrder) {
                $executionOrder[] = 3;
                return 'foobar';
            }
        );

        $commandBus = new CommandBus([$middleware1, $middleware2, $middleware3]);

        $this->assertEquals('foobar', $commandBus->handle(new AddTaskCommand()));
        $this->assertEquals([1, 2, 3], $executionOrder);
    }

    public function testSingleMiddlewareWorks()
    {
        $middleware = Mockery::mock(Middleware::class);
        $middleware->shouldReceive('execute')->once()->andReturn('foobar');

        $commandBus = new CommandBus([$middleware]);

        $this->assertEquals(
            'foobar',
            $commandBus->handle(new AddTaskCommand())
        );
    }

    public function testNoMiddlewarePerformsASafeNoop()
    {
        (new CommandBus([]))->handle(new AddTaskCommand());
    }
}
