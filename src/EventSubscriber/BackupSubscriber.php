<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Backup;
use App\Helper\FlysystemHelper;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class BackupSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly FlysystemHelper $flysystemHelper)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
        ];
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function postPersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if (!$entity instanceof Backup) {
            return;
        }

        $this->flysystemHelper->upload($entity);
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function postRemove(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if (!$entity instanceof Backup) {
            return;
        }

        $this->flysystemHelper->remove($entity);
    }
}
