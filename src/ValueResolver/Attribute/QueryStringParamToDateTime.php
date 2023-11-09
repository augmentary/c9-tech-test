<?php
declare(strict_types=1);

namespace App\ValueResolver\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class QueryStringParamToDateTime
{
    public function __construct(
        public ?string $param = null,
        public string $format = \DateTimeInterface::ATOM
    ) {
    }
}
