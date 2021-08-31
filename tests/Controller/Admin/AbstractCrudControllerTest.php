<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Controller\AbstractControllerTest;

abstract class AbstractCrudControllerTest extends AbstractControllerTest
{
    public function testIndex(): void
    {
        $url = $this->getCrudActionUrl('index');

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    public function testNew(): void
    {
        $url = $this->getCrudActionUrl('new');

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    abstract protected function getControllerClass(): string;

    protected function getCrudActionUrl(string $action, ?int $entityId = null): string
    {
        $generator = $this->adminUrlGenerator->setController($this->getControllerClass())
            ->setAction($action);

        if (null !== $entityId) {
            $generator->setEntityId($entityId);
        }

        return $generator->generateUrl();
    }
}
