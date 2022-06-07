<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatableMessage;

final class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin');
        }

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
            'page_title' => 'DbSaver',
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('admin'),
            'username_label' => new TranslatableMessage('security.login.email'),
            'password_label' => new TranslatableMessage('security.login.password'),
            'sign_in_label' => new TranslatableMessage('security.login.submit'),
            'username_parameter' => 'email',
            'password_parameter' => 'password',
            'forgot_password_enabled' => true,
            'forgot_password_path' => $this->generateUrl('app_forgot_password_request'),
            'remember_me_enabled' => true,
        ]);
    }
}
