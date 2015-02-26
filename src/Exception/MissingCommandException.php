<?php

namespace League\Tactician\Exception;

use League\Tactician\Command;

/**
 * Command has not been added to the mapping array.
 */
class MissingCommandException extends \OutOfBoundsException implements Exception
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @param Command $command
     *
     * @return static
     */
    public static function forCommand(Command $command)
    {
        $exception = new static('The command ' . get_class($command) . ' has not been addeded to the mapping configuration.');
        $exception->command = $command;

        return $exception;
    }

    /**
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }
}
