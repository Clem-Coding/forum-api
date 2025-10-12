<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Comment;
use App\Entity\Topic;
use Symfony\Bundle\SecurityBundle\Security;

class CommentPersister implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {

        //if the entity is a Comment and has no user, set the user to the currently authenticated user
        if ($data instanceof Comment) {
            if (null === $data->getUser()) {
                $user = $this->security->getUser();
                if ($user) {
                    $data->setUser($user);
                }
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
