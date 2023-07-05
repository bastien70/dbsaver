<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Backup;
use App\Helper\FlysystemHelper;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Backup::class)]
#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: Backup::class)]
class BackupSubscriber
{
    public function __construct(private readonly FlysystemHelper $flysystemHelper)
    {
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function postPersist(Backup $backup, PostPersistEventArgs $event): void
    {
        $this->flysystemHelper->upload($backup);
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function postRemove(Backup $backup, PostRemoveEventArgs $event): void
    {
        $this->flysystemHelper->remove($backup);
    }
}
