<?php

namespace League\Tactician\Handler\Locator;

use League\Tactician\Command;
use League\Tactician\Exception\MissingCommandException;

/**
 * Fetch handler instances from an in-memory collection.
 *
 * This locator allows you to bind a handler fqcn to receive commands of a
 * certain class name. For example:
 *
 *      // Wire everything together
 *      $mapping = [
 *          '\Path\To\CommandHandler' => '\Path\To\Command',
 *      ];
 *
 *      $inMemoryLocator->addHandlers($mapping);
 *
 *      // Returns $myHandler
 *      $inMemoryLocator->getHandlerForCommand(new My\TaskAddedCommand());
 */
class InMemoryLocator implements HandlerLocator
{
    /**
     * @var array
     */
    protected $handlers = [];

    /**
     * @param array $commandClassToHandlerMap
     */
    public function __construct(array $commandClassToHandlerMap = [])
    {
        $this->addHandlers($commandClassToHandlerMap);
    }

    /**
     * Bind a handler instance to receive all commands with a certain class
     *
     * @param object $handler Handler to receive class
     * @param string $commandClassName Command class e.g. "My\TaskAddedCommand"
     */
    public function addHandler($handler, $commandClassName)
    {
        $this->handlers[$commandClassName] = $handler;
    }

    /**
     * Allows you to add multiple handlers at once.
     *
     * The map should be an array in the format of:
     *  [
     *      '\Path\To\AddTaskCommand'      => '\Path\To\someHandlerInstance',
     *      '\Path\To\CompleteTaskCommand' => '\Path\To\someHandlerInstance',
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
     * @param Command $command
     * @return object
     * @throws MissingHandlerException
     */
    public function getHandlerForCommand(Command $command)
    {
        $className = get_class($command);

        if (!isset($this->handlers[$className])) {
            throw MissingCommandException::forCommand($command);
        }

        return new $this->handlers[$className];
    }
}
