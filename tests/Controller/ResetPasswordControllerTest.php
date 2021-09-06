<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ResetPasswordControllerTest extends AbstractControllerTest
{
    private ResetPasswordHelperInterface $resetPasswordHelper;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetPasswordHelper = self::getContainer()->get(ResetPasswordHelperInterface::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    public function testRequestPasswordReset(): void
    {
        self::$client->request('GET', '/reset-password');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('_submit', ['reset_password_request' => [
            'email' => 'user@test.com',
        ]]);

        self::assertResponseRedirects('/reset-password/check-email');
    }

    /**
     * @throws ResetPasswordExceptionInterface
     * @throws TooManyPasswordRequestsException
     */
    public function testResetPassword()
    {
        $user = $this->userRepository->find(self::USER_ROLE_ADMIN);
        $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        self::$client->request('GET', sprintf('/reset-password/reset/%s', $resetToken->getToken()));
        self::assertResponseRedirects('/reset-password/reset');
        self::$client->followRedirect();

        self::$client->submitForm('_submit', [
            'reset_password[plainPassword][first]' => 'resetPassword',
            'reset_password[plainPassword][second]' => 'resetPassword',
        ]);

        self::assertResponseRedirects('/login');
        self::$client->followRedirect();
        self::assertSelectorExists('.alert-success');

        $this->submitLogin('admin@test.com', 'resetPassword');
        self::assertResponseRedirects();
        self::assertTrue(self::getSecurityDataCollector()->isAuthenticated());
    }
}
