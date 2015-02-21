<?php

namespace League\Tactician\Tests\Fixtures\Container;


class Mailer {

    public function send($to, $subject, $body)
    {
        echo sprintf(
            'An email has been sent to %s, with the subject of %s. It says: `%s`',
            $to,
            $subject,
            $body
        );
    }

}
