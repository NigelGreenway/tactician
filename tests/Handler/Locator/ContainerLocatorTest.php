<?php

namespace League\Tactician\Tests\Handler\Locator;

use League\Tactician\Handler\Locator\ContainerLocator;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use League\Tactician\Tests\Fixtures\Command\AddTaskCommand;
use League\Tactician\Tests\Fixtures\Command\DeleteTaskCommand;
use League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand;
use League\Container\Container;

class ContainerLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var containerLocator
     */
    private $containerLocator;

    protected function setUp()
    {
        $dic = [
            'di' => [
                'mailer' => [
                    'class' => '\League\Tactician\Tests\Fixtures\Container\Mailer',
                ],
                'CompleteTaskCommandHandler' => [
                    'class' => '\League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand',
                    'attributes' => [
                        'mailer',
                    ],
                ],
            ],
        ];

        $mapping = [
            'League\Tactician\Tests\Fixtures\Command\CompleteTaskCommand' => 'League\Tactician\Tests\Fixtures\Handler\ConcreteMethodsHandler',
            'League\Tactician\Tests\Fixtures\Command\AddTaskCommand'      => 'League\Tactician\Tests\Fixtures\Handler\DynamicMethodsHandler',
        ];

        $container              = new Container($dic);
        $this->containerLocator = new ContainerLocator($container, $mapping);
    }

    public function testHandlerIsReturnedForSpecificClass()
    {
        $this
            ->assertInstanceOf(
                '\League\Tactician\Tests\Fixtures\Handler\ConcreteMethodsHandler',
                $this->containerLocator->getHandlerForCommand(new CompleteTaskCommand())
            );
    }

    /**
     * @expectedException \League\Tactician\Exception\MissingCommandException
     */
    public function testMissingCommandExceptionThrownOnInvalidMappingConfig()
    {
        $this->containerLocator->getHandlerForCommand(new DeleteTaskCommand());
    }
}
