<?php

namespace App\Exception;

class NotFoundException extends \Exception
{
    public function __construct(
        string $message,
        protected readonly string $parameter,
        protected readonly mixed $value,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getParameter(): string
    {
        return $this->parameter;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
