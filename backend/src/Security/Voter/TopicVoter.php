<?php

namespace App\Security\Voter;

use App\Entity\Topic;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class TopicVoter extends Voter
{
    public const EDIT = 'TOPIC_EDIT';
    public const DELETE = 'TOPIC_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Topic;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // admins can do anything
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        /** @var Topic $topic */
        $topic = $subject;

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                return $topic->getUser() === $user;
        }

        return false;
    }
}
