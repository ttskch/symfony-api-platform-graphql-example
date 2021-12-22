<?php

namespace App\ApiPlatform\DataProvider\Traits;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ArrayPaginator;
use Doctrine\ORM\QueryBuilder;

/**
 * @property iterable<QueryCollectionExtensionInterface> $collectionExtensions
 */
trait CollectionDataProviderTrait
{
    protected function getResult(QueryBuilder $qb, string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $queryNameGenerator = new QueryNameGenerator();

        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($qb, $queryNameGenerator, $resourceClass, $operationName, $context);

            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                return $extension->getResult($qb);
            }
        }

        $array = $qb->getQuery()->getResult();

        return new ArrayPaginator($array, 0, count($array));
    }
}
