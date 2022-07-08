<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\BackupCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BackupCrudControllerTest extends AbstractCrudControllerTest
{
    public function testNew(): void
    {
        $url = $this->getCrudActionUrl('new');

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);
    }

    public function testEdit(): void
    {
        $url = $this->getCrudActionUrl('edit', 1);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);
    }

    public function testDelete(): void
    {
        $url = $this->getCrudActionUrl(Action::DELETE, self::USER_ROLE_USER);

        self::$client->request(Request::METHOD_GET, $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request(Request::METHOD_GET, $url);
        $responseCode = self::$client->getResponse()->getStatusCode();

        // If not asserting for both 302 and 403, this will either fail locally or in the CI
        self::assertTrue(\in_array($responseCode, [Response::HTTP_FOUND, Response::HTTP_FORBIDDEN], true));
    }

    protected function getControllerClass(): string
    {
        return BackupCrudController::class;
    }
}
