<?php

namespace App\ApiPlatform\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\ApiPlatform\DataProvider\Traits\CollectionDataProviderTrait;
use App\Entity\Post;
use App\Repository\PostRepository;

class PostCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    use CollectionDataProviderTrait;

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

        return $this->getResult($qb, $resourceClass, $operationName, $context);
    }
}
