<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\Response;

class UserCrudControllerTest extends AbstractCrudControllerTest
{
    public function testNewWithSimpleUser(): void
    {
        $url = $this->getActionUrl(Action::NEW);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

        // Simple user with ROLE_USER
        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(
            Response::HTTP_FORBIDDEN,
            'User with only ROLE_USER could not access New User page'
        );
    }

    public function testNewWithSimpleAdmin(): void
    {
        $url = $this->getActionUrl(Action::NEW);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

        // Simple user with ROLE_USER
        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful('User with ROLE_ADMIN could add another User');
    }

    public function testEditWithSimpleUser(): void
    {
        $url = $this->getActionUrl(Action::EDIT, self::USER_ROLE_USER);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

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
        $url = $this->getActionUrl(Action::EDIT, self::USER_ROLE_USER);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

        // Simple user with ROLE_ADMIN
        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful('User with ROLE_ADMIN could edit another User');
    }

    protected function getControllerClass(): string
    {
        return UserCrudController::class;
    }
}
