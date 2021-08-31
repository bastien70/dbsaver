<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Model\SettingsModel;
use App\Form\Type\SettingsType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

final class UserController extends AbstractController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    #[Route('/settings', name: 'app_user_settings')]
    public function settings(Request $request): Response
    {
        $user = $this->getUser();
        $settings = SettingsModel::createFromUser($user);
        $form = $this->createForm(SettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setLocale($settings->locale);
            $request->getSession()->set('_locale', $settings->locale);
            if (null !== $settings->newPassword) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $settings->newPassword));
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', new TranslatableMessage('user.settings.flash_success'));

            return $this->redirect($this->adminUrlGenerator->setRoute('app_user_settings')->generateUrl());
        }

        return $this->renderForm('user/settings.html.twig', [
            'form' => $form,
        ]);
    }
}
