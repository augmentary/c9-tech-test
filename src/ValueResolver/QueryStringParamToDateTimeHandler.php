<?php
declare(strict_types=1);

namespace App\ValueResolver;

use App\Exception\InvalidDataException;
use App\Exception\MissingDataException;
use App\ValueResolver\Attribute\QueryStringParamToDateTime;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTaggedItem(priority: 120)]
readonly class QueryStringParamToDateTimeHandler implements ValueResolverInterface
{
    /**
     * @throws MissingDataException
     * @throws InvalidDataException
     * @return array{0: ?\DateTimeImmutable}
     */
    public function resolve(
        Request $request,
        ArgumentMetadata $argument
    ): iterable {
        /** @var ?QueryStringParamToDateTime $configuration */
        $configuration = $argument->getAttributes(QueryStringParamToDateTime::class)[0] ?? null;
        if (!$configuration) {
            return [];
        }

        $required = !$argument->isNullable();
        $param = $configuration->param ?? $argument->getName();
        $present = $request->query->has($param);

        if(!$present) {
            if ($required) {
                throw new MissingDataException("Missing value for $param", [$param]);
            }
            return [null];
        }

        $value = $request->query->get($param);
        $result = \DateTimeImmutable::createFromFormat($configuration->format, $value);

        if(false === $result) {
            throw new InvalidDataException("Invalid value for $param", [$param => $value]);
        }

        return [$result];
    }
}