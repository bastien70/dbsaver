<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Controller\AbstractControllerTest;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

abstract class AbstractCrudControllerTest extends AbstractControllerTest
{
    private AdminUrlGenerator $adminUrlGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->adminUrlGenerator = self::getContainer()->get(AdminUrlGenerator::class);
    }

    public function testIndex(): void
    {
        $url = $this->getActionUrl('index');

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

        $this->loginAsUser1();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    public function testNew(): void
    {
        $url = $this->getActionUrl('new');

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

        $this->loginAsUser1();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    abstract protected function getControllerClass(): string;

    protected function getActionUrl(string $action, ?int $entityId = null): string
    {
        $generator = $this->adminUrlGenerator->setController($this->getControllerClass())
            ->setAction($action);

        if (null !== $entityId) {
            $generator->setEntityId($entityId);
        }

        return $generator->generateUrl();
    }
}
