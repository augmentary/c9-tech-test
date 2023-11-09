<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Exception\MissingDataException;
use App\Exception\NotFoundException;
use App\ValueResolver\Attribute\QueryStringParamToEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTaggedItem(priority: 120)]
readonly class QueryStringParamToEntityHandler implements ValueResolverInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @throws MissingDataException
     * @throws NotFoundException
     * @return array{0: ?object}
     */
    public function resolve(
        Request $request,
        ArgumentMetadata $argument
    ): iterable {
        //todo: pull the common functionality out between this and QueryStringParamToDateTimeHandler to a helper class
        /** @var ?QueryStringParamToEntity $configuration */
        $configuration = $argument->getAttributes(QueryStringParamToEntity::class)[0] ?? null;
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

        $repo = $this->entityManager->getRepository($argument->getType());
        assert($repo instanceof EntityRepository);

        $field = $configuration->field ?? $argument->getName();
        $value = $request->query->get($param);
        $result = $repo->findOneBy([$field => $value]);

        if(null === $result) {
            throw new NotFoundException("Invalid value for $param", $param, $value);
        }

        return [$result];
    }
}
