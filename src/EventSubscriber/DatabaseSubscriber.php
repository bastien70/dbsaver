<?php

namespace App\EventSubscriber;

use App\Entity\Database;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class DatabaseSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Encryptor $encryptor,
        private Security $security
    ){}

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => 'beforePersistedEvent',
            BeforeEntityUpdatedEvent::class => 'beforeUpdatedEvent',
        ];
    }

    public function beforePersistedEvent(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if(!($entity instanceof Database))
        {
            return;
        }

        $entity->setDbPassword($this->encryptPassword($entity->getDbPlainPassword()))
            ->setDbPlainPassword(null)
            ->setUser($this->security->getUser());
    }

    public function beforeUpdatedEvent(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if(!($entity instanceof Database))
        {
            return;
        }

        if($entity->getDbPlainPassword())
        {
            $entity->setDbPassword($this->encryptPassword($entity->getDbPlainPassword()))
                ->setDbPlainPassword(null);
        }
    }

    private function encryptPassword(string $plainPassword)
    {
        return $this->encryptor->encrypt($plainPassword);
    }
}