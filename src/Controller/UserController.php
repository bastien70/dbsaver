<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Model\SettingsModel;
use App\Form\Type\SettingsType;
use Doctrine\ORM\EntityManagerInterface;
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
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/settings', name: 'app_user_settings', methods: ['GET', 'POST'])]
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

            $this->em->flush();
            $this->addFlash('success', new TranslatableMessage('user.settings.flash_success'));

            return $this->redirect($this->adminUrlGenerator->setRoute('app_user_settings')->generateUrl());
        }

        return $this->renderForm('user/settings.html.twig', [
            'form' => $form,
        ]);
    }
}
