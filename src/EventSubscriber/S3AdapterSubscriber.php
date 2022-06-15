<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Enum\S3Provider;
use App\Entity\S3Adapter;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class S3AdapterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Encryptor $encryptor,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => 'beforePersistedEvent',
            BeforeEntityUpdatedEvent::class => 'beforeUpdatedEvent',
        ];
    }

    public function beforePersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof S3Adapter) {
            return;
        }

        if (S3Provider::OTHER !== $entity->getS3Provider()) {
            $entity->setS3Endpoint(null);
        }

        $this->handleAdapterSecretChange($entity);
    }

    public function beforeUpdatedEvent(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof S3Adapter) {
            return;
        }

        $this->handleAdapterSecretChange($entity);
    }

    private function handleAdapterSecretChange(S3Adapter $s3Adapter): void
    {
        if (null !== $s3Adapter->getS3PlainAccessSecret()) {
            $s3Adapter->setS3AccessSecret($this->encryptor->encrypt($s3Adapter->getS3PlainAccessSecret()))
                ->setS3PlainAccessSecret(null);
        }
    }
}
