<?php

namespace League\Tactician\Tests\Handler\Locator;

use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Tests\Fixtures\Command\AddTaskCommand;
use League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand;
use League\Tactician\Tests\Fixtures\Handler\ConcreteMethodsHandler;
use stdClass;

class InMemoryLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemoryLocator
     */
    private $inMemoryLocator;

    protected function setUp()
    {
        $this->inMemoryLocator = new InMemoryLocator();
    }

    public function testHandlerIsReturnedForSpecificClass()
    {
        $handler = new stdClass();

        $this->inMemoryLocator->addHandler(get_class($handler), CompleteTaskCommand::class);

        $this->assertInstanceOf(
            'stdClass',
            $this->inMemoryLocator->getHandlerForCommand(new CompleteTaskCommand())
        );
    }

    public function testConstructorAcceptsMapOfCommandClassesToHandlers()
    {
        $commandToHandlerMap = [
            AddTaskCommand::class => ConcreteMethodsHandler::class,
            CompleteTaskCommand::class => ConcreteMethodsHandler::class,
        ];

        $locator = new InMemoryLocator($commandToHandlerMap);

        $this->assertSame(
            $commandToHandlerMap[AddTaskCommand::class],
            get_class($locator->getHandlerForCommand(new AddTaskCommand()))
        );

        $this->assertSame(
            $commandToHandlerMap[CompleteTaskCommand::class],
            get_class($locator->getHandlerForCommand(new CompleteTaskCommand()))
        );
    }

    /**
     * @expectedException \League\Tactician\Exception\MissingCommandException
     */
    public function testHandlerMissing()
    {
        $this->inMemoryLocator->getHandlerForCommand(new CompleteTaskCommand());
    }
}
