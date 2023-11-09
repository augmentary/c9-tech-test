<?php

namespace App\Exception;

class InvalidDataException extends \Exception
{
    /**
     * @param array<string, string> $vars
     */
    public function __construct(
        string $message,
        protected array $vars,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<string, string>
     */
    public function getVars(): array
    {
        return $this->vars;
    }
}
