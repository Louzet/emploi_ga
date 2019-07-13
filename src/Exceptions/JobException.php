<?php


namespace App\Exceptions;


class JobException extends \InvalidArgumentException
{

    /**
     * JobException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return $this->message;
    }

    public static function EmptyParamsException()
    {
        $message = "title is required";
        return new self($message, 400);
    }
}