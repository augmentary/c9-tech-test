<?php

namespace App\Exception;

class MissingDataException extends \Exception
{
    /**
     * @param list<string> $vars
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
     * @return list<string>
     */
    public function getVars(): array
    {
        return $this->vars;
    }
}
