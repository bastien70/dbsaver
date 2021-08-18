<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Database;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class DatabaseSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Encryptor $encryptor,
        private Security $security,
    ){}

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'beforePersistedEvent',
            BeforeEntityUpdatedEvent::class => 'beforeUpdatedEvent',
        ];
    }

    public function beforePersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof Database) {
            return;
        }

        $this->handleDatabasePasswordChange($entity);

        $user = $this->security->getUser();
        assert($user instanceof User);
        $entity->setUser($user);
    }

    public function beforeUpdatedEvent(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof Database) {
            return;
        }

        $this->handleDatabasePasswordChange($entity);
    }

    private function handleDatabasePasswordChange(Database $database): void
    {
        if (null !== $database->getDbPlainPassword()) {
            $database->setDbPassword($this->encryptor->encrypt($database->getDbPlainPassword()))
                ->setDbPlainPassword(null);
        }
    }
}
