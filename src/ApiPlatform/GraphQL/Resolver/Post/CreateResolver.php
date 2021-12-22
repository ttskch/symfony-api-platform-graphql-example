<?php

namespace App\ApiPlatform\GraphQL\Resolver\Post;

use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\Post;

class CreateResolver implements MutationResolverInterface
{
    /**
     * @param Post|null $post
     */
    public function __invoke($post, array $context): ?Post
    {
        if (!$post instanceof Post) {
            return null;
        }

        $post->setDate(new \DateTime($context['args']['input']['date'] ?? 'today'));

        return $post;
    }
}
