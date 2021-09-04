<?php

declare(strict_types=1);

namespace App\Tests\Controller;

class ResetPasswordControllerTest extends AbstractControllerTest
{
    public function testRequestPasswordReset()
    {
        self::$client->request('GET', '/reset-password');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('_submit', ['reset_password_request' => [
            'email' => 'user@test.com',
        ]]);

        self::assertResponseRedirects('/reset-password/check-email');
    }
}
