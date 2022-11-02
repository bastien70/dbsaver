<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Model\EnableTwoFactorAuthenticationModel;
use App\Form\Model\SettingsModel;
use App\Form\Type\DisableTwoFactorAuthenticationType;
use App\Form\Type\EnableTwoFactorAuthenticationType;
use App\Form\Type\SettingsType;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatableMessage;

#[Route('/user', name: 'app_user_')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
        private readonly TotpAuthenticatorInterface $totpAuthenticator,
    ) {
    }

    #[Route('/settings', name: 'settings', methods: ['GET', 'POST'])]
    public function settings(Request $request): Response
    {
        $user = $this->getUser();
        \assert($user instanceof User);
        $settings = SettingsModel::createFromUser($user);
        $form = $this->createForm(SettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setLocale($settings->locale);
            $user->setReceiveAutomaticEmails($settings->receiveAutomaticEmails);
            $request->getSession()->set('_locale', $settings->locale);
            if (null !== $settings->newPassword) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $settings->newPassword));
            }

            $this->userRepository->save($user);
            $this->addFlash('success', new TranslatableMessage('user.settings.flash_success'));

            return $this->redirect($this->adminUrlGenerator->setRoute('app_user_settings')->generateUrl());
        }

        return $this->renderForm('user/settings.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/enable-2fa', name: 'enable_2fa', methods: ['GET', 'POST'])]
    public function enable2fa(Request $request, UserInterface $user): Response
    {
        \assert($user instanceof User);
        if ($user->isTotpAuthenticationEnabled()) {
            $this->addFlash('danger', new TranslatableMessage('user.enable_2fa.already_enabled'));

            return $this->redirect($this->adminUrlGenerator->setRoute('app_user_settings')->generateUrl());
        }

        if (null === $user->getTotpSecret()) {
            $user->setTotpSecret($this->totpAuthenticator->generateSecret());
            $this->userRepository->save($user);
        }

        $model = new EnableTwoFactorAuthenticationModel($user);
        $form = $this->createForm(EnableTwoFactorAuthenticationType::class, $model);
        $form->handleRequest($request);
        dump($request->request->all(), $form->isSubmitted(), $form->getData());

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setTotpEnabled(true);
            $this->userRepository->save($user);
            $this->addFlash('success', new TranslatableMessage('user.enable_2fa.flash_success'));

            return $this->redirect($this->adminUrlGenerator->setRoute('app_user_settings')->generateUrl());
        }

        return $this->renderForm('user/enable_2fa.html.twig', [
            'form' => $form,
            'qr_code' => $this->totpAuthenticator->getQRContent($user),
        ]);
    }

    #[Route('/disable-2fa', name: 'disable_2fa', methods: ['GET', 'POST'])]
    public function disable2fa(Request $request, UserInterface $user): Response
    {
        \assert($user instanceof User);
        if (!$user->isTotpAuthenticationEnabled()) {
            $this->addFlash('danger', new TranslatableMessage('user.disable_2fa.not_enabled'));

            return $this->redirect($this->adminUrlGenerator->setRoute('app_user_settings')->generateUrl());
        }

        $form = $this->createForm(DisableTwoFactorAuthenticationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setTotpSecret(null);
            $user->setTotpEnabled(false);
            $this->userRepository->save($user);
            $this->addFlash('success', new TranslatableMessage('user.disable_2fa.flash_success'));

            return $this->redirect($this->adminUrlGenerator->setRoute('app_user_settings')->generateUrl());
        }

        return $this->renderForm('user/disable_2fa.html.twig', [
            'form' => $form,
        ]);
    }
}
