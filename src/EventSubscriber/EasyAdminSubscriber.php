<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatableMessage;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => ['flashMessageAfterPersist'],
            AfterEntityUpdatedEvent::class => ['flashMessageAfterUpdate'],
            AfterEntityDeletedEvent::class => ['flashMessageAfterDelete'],
        ];
    }

    public function flashMessageAfterPersist(AfterEntityPersistedEvent $event): void
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', new TranslatableMessage('content_admin.flash_message.create', [
            '%name%' => (string) $event->getEntityInstance(),
        ]));
    }

    public function flashMessageAfterUpdate(AfterEntityUpdatedEvent $event): void
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', new TranslatableMessage('content_admin.flash_message.update', [
            '%name%' => (string) $event->getEntityInstance(),
        ]));
    }

    public function flashMessageAfterDelete(AfterEntityDeletedEvent $event): void
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', new TranslatableMessage('content_admin.flash_message.delete', [
            '%name%' => (string) $event->getEntityInstance(),
        ]));
    }
}
