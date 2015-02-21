<?php
require __DIR__ . '/../vendor/autoload.php';

// The basic code from example 1 that we reuse in future examples.

use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Command;

class RegisterUserCommand implements Command
{
    public $emailAddress;
    public $password;
}

class RegisterUserHandler
{
    public function handleRegisterUserCommand(RegisterUserCommand $command)
    {
        // Do your core application logic here. Don't actually echo things. :)
        echo "User {$command->emailAddress} was registered!\n";
    }
}

$mapping = [
    'RegisterUserCommand' => 'RegisterUserHandler',
];

$locator = new InMemoryLocator($mapping);

$handlerMiddleware = new League\Tactician\Handler\CommandHandlerMiddleware(
    $locator,
    new HandleClassNameInflector()
);

$commandBus = new \League\Tactician\CommandBus([$handlerMiddleware]);

$ruc = new RegisterUserCommand;
$ruc->emailAddress = 'a@b.com';
$ruc->password = 'secret';


try {
    $commandBus->handle($ruc);
} catch (\Exception $e) {
    echo $e->getMessage();
}
