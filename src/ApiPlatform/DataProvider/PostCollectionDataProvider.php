<?php

namespace App\ApiPlatform\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ArrayPaginator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Post;
use App\Repository\PostRepository;

class PostCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        private PostRepository $repository,
        private iterable $collectionExtensions,
    ) {}

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Post::class;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $qb = $this->repository->createQueryBuilder('p')
            ->andWhere('p.published = 1')
        ;

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
