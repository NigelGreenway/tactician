<?php

namespace League\Tactician\Handler\Locator;

use League\Tactician\Command;
use League\Tactician\Exception\MissingCommandException;
use League\Tactician\Exception\MissingContainerServiceException;

use League\Container\Container;
use League\Container\Exception\ReflectionException;

/**
 * Fetch handler instances from an in-memory collection.
 *
 * This locator allows you to bind a handler fqcn to receive commands of a
 * certain class name. For example:
 */
class ContainerLocator implements HandlerLocator
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var []
     */
    protected $commandToHandlerIdMap = [];

    /**
     * Class constructor
     *
     * @param Container $container
     * @param array     $commandClassToHandlerMap
     */
    public function __construct(
        Container $container,
        array $commandClassToHandlerMap = []
    ) {
        $this->container = $container;

        if (empty($commandClassToHandlerMap) === false) {
            $this->addHandlers($commandClassToHandlerMap);
        }
    }

    /**
     * Bind a handler instance to receive all commands with a certain class
     *
     * @param string $handlerId        Handler to receive class
     * @param string $commandClassName Command class e.g. "My\TaskAddedCommand"
     */
    public function addHandler($handlerId, $commandClassName)
    {
        $this->commandToHandlerIdMap[$commandClassName] = $handlerId;
    }

    /**
     * Allows you to add multiple handlers at once.
     *
     * The map should be an array in the format of:
     *  [
     *      'AddTaskCommand'      => 'AddTaskCommandHandler',
     *      'CompleteTaskCommand' => 'CompleteTaskCommandHandler',
     *  ]
     *
     * @param array $commandClassToHandlerMap
     */
    protected function addHandlers(array $commandClassToHandlerMap)
    {
        foreach ($commandClassToHandlerMap as $commandClass => $handler) {
            $this->addHandler($handler, $commandClass);
        }
    }

    /**
     * Retrieve handler for the given command
     *
     * @param  Command $command
     * @return object
     * @throws MissingHandlerException
     */
    public function getHandlerForCommand(Command $command)
    {
        $className = get_class($command);

        if (!isset($this->commandToHandlerIdMap[$className])) {
            throw MissingCommandException::forCommand($command);
        }

        $serviceId = $this->commandToHandlerIdMap[$className];

        try {
            return $this->container->get($serviceId);
        } catch (ReflectionException $e) {
            throw MissingContainerServiceException::forCommand($command);
        }

    }
}
