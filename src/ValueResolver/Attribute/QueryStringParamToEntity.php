<?php

declare(strict_types=1);

namespace App\ValueResolver\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class QueryStringParamToEntity
{
    public function __construct(
        public ?string $param = null,
        public string $field = 'id'
    ) {
    }
}
