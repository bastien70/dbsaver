<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Controller\Admin\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\Response;

class UserCrudControllerTest extends AbstractCrudControllerTest
{
    public function testCountMenuLinksWithSimpleUser(): void
    {
        self::$client->request('GET', '/');
        $this->loginAsUser();

        $url = $this->adminUrlGenerator->setController(DashboardController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        $crawler = self::$client->request('GET', $url);
        self::assertResponseIsSuccessful();

        $menuItems = $crawler->filter('#main-menu')->filter('.menu-item');
        self::assertCount(9, $menuItems, 'Dashboard should contains 9 menu items with ROLE_USER');
    }

    public function testCountMenuLinksWithAdminUser(): void
    {
        self::$client->request('GET', '/');
        $this->loginAsAdmin();

        $url = $this->adminUrlGenerator->setController(DashboardController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        $crawler = self::$client->request('GET', $url);
        self::assertResponseIsSuccessful();

        $menuItems = $crawler->filter('#main-menu')->filter('.menu-item');
        self::assertCount(10, $menuItems, 'Dashboard should contains 10 menu items with ROLE_ADMIN');
    }

    public function testNewWithSimpleUser(): void
    {
        $url = $this->getCrudActionUrl(Action::NEW);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        // Simple user with ROLE_USER
        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(
            Response::HTTP_FORBIDDEN,
            'User with only ROLE_USER could not access New User page'
        );
    }

    public function testNewWithAdmin(): void
    {
        $url = $this->getCrudActionUrl(Action::NEW);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        // Simple user with ROLE_USER
        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful('User with ROLE_ADMIN could add another User');
    }

    public function testEditWithSimpleUser(): void
    {
        $url = $this->getCrudActionUrl(Action::EDIT, self::USER_ROLE_USER);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        // Simple user with ROLE_USER
        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(
            Response::HTTP_FORBIDDEN,
            'User with only ROLE_USER could not edit another User'
        );
    }

    public function testEditWithAdminUser(): void
    {
        $url = $this->getCrudActionUrl(Action::EDIT, self::USER_ROLE_USER);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        // Simple user with ROLE_ADMIN
        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful('User with ROLE_ADMIN could edit another User');
    }

    public function testDeleteWithAdminUser(): void
    {
        $url = $this->getCrudActionUrl(Action::DELETE, self::USER_ROLE_USER);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseRedirects();
    }

    public function testDeleteWithSimpleUser(): void
    {
        $url = $this->getCrudActionUrl(Action::DELETE, self::USER_ROLE_ADMIN);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);
    }

    protected function getControllerClass(): string
    {
        return UserCrudController::class;
    }
}
