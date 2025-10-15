<?php

namespace App\Factory;

use App\Entity\Topic;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Topic>
 */
final class TopicFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct() {}

    #[\Override]
    public static function class(): string
    {
        return Topic::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'content' => self::faker()->text(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year', '-2 months')),
            'title' => self::faker()->text(150),
            'updatedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year', '-2 months')),
            'user' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Topic $topic): void {})
        ;
    }
}
