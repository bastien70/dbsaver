<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\FtpAdapter;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\CacheInterface;

class FtpAdapterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Encryptor $encryptor,
        private readonly CacheInterface $cache,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'beforePersistedEvent',
            BeforeEntityUpdatedEvent::class => 'beforeUpdatedEvent',
            BeforeEntityDeletedEvent::class => 'beforeDeletedEvent',
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function beforePersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof FtpAdapter) {
            return;
        }

        $this->invalidateCache();

        $this->handleAdapterPasswordChange($entity);
    }

    public function beforeUpdatedEvent(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof FtpAdapter) {
            return;
        }

        $this->handleAdapterPasswordChange($entity);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function beforeDeletedEvent(BeforeEntityDeletedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof FtpAdapter) {
            return;
        }

        $this->invalidateCache();
    }

    private function handleAdapterPasswordChange(FtpAdapter $s3Adapter): void
    {
        if (null !== $s3Adapter->getFtpPlainPassword()) {
            $s3Adapter->setFtpPassword($this->encryptor->encrypt($s3Adapter->getFtpPlainPassword()))
                ->setFtpPlainPassword(null);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function invalidateCache(): void
    {
        $this->cache->delete('ftp_adapter_count');
    }
}
