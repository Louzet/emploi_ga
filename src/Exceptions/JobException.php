<?php

namespace App\Exceptions;


class JobException extends \Exception
{

    /**
     * JobException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code = 500)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return $this->message;
    }

    public static function EmptyParamsException()
    {
        $message = "some fields are required";
        return new static($message, 400);
    }
}