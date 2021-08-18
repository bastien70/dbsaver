<?php

declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\EventSubscriber\EasyAdminSubscriber;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

final class EasyAdminSubscriberTest extends TestCase
{
    public function testEventDispatching(): void
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects($this->exactly(3))->method('getSession')->willReturn(new Session(new MockArraySessionStorage()));

        $entity = new User();
        $entity->setEmail('test');

        $subscriber = new EasyAdminSubscriber($requestStack);
        $event1 = new AfterEntityPersistedEvent($entity);
        $event2 = new AfterEntityUpdatedEvent($entity);
        $event3 = new AfterEntityDeletedEvent($entity);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event1);
        $dispatcher->dispatch($event2);
        $dispatcher->dispatch($event3);
    }
}
