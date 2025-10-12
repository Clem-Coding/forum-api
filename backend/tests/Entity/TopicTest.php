<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TopicTest extends TestCase
{
    public function testTopicEntity(): void
    {
        $topic = new Topic();
        $topic->setTitle('Mon titre');
        $topic->setContent('Mon contenu de test');

        $this->assertSame('Mon titre', $topic->getTitle());
        $this->assertSame('Mon contenu de test', $topic->getContent());

        $user = new User();
        $topic->setUser($user);
        $this->assertSame($user, $topic->getUser());

        $comment = new Comment();
        $topic->addComment($comment);
        $this->assertTrue($topic->getComments()->contains($comment));

        $topic->removeComment($comment);
        $this->assertFalse($topic->getComments()->contains($comment));
    }
}
