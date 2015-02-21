<?php
require __DIR__ . '/../vendor/autoload.php';

// The basic code from example 1 that we reuse in future examples.

use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use League\Tactician\Handler\Locator\ContainerLocator;
use League\Tactician\Command;

use League\Container\Container;

class Mailer
{
    public function send($to, $from = 'from@me.com', $subject, $body)
    {
        echo nl2br($body); // this normally would send an email with the requested params
    }
}

class RegisterUserCommand implements Command
{
    public $emailAddress;
    public $password;
}

class RegisterUserHandler
{
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handleRegisterUserCommand(RegisterUserCommand $command)
    {
        // Do your core application logic here. Don't actually echo things. :)
       $this
        ->mailer
        ->send(
            $command->emailAddress,
            null,
            "Your shiny password",
            sprintf(
                "Here is your shiny new password: <i>%s</i>\n\nThank you.",
                $command->password
            )
        );
    }
}

$config = [
    'di' => [
        'mailer' => [
            'class' => 'Mailer',
        ],
        'RegisterUserCommandHandler' => [
            'class'     => 'RegisterUserCommandHandler',
            'arguments' => [
                'mailer',
            ]
        ],
    ],
];

$container = new Container($config);

$mapping = [
    'RegisterUserCommand' => 'RegisterUserHandler',
];

$locator = new ContainerLocator($container, $mapping);

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
    echo '<pre>';
    print_r($e->getTraceAsString());
    echo '</pre>';
}
