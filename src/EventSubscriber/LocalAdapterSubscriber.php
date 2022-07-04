<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\LocalAdapter;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\CacheInterface;

class LocalAdapterSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly CacheInterface $cache)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'beforePersistedEvent',
            BeforeEntityDeletedEvent::class => 'beforeDeletedEvent',
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function beforePersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof LocalAdapter) {
            return;
        }

        $this->invalidateCache();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function beforeDeletedEvent(BeforeEntityDeletedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof LocalAdapter) {
            return;
        }

        $this->invalidateCache();
    }

    /**
     * @throws InvalidArgumentException
     */
    private function invalidateCache(): void
    {
        $this->cache->delete('local_adapter_count');
    }
}
