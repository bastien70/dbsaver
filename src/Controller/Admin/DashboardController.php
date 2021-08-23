<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Backup;
use App\Entity\Database;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\RouteMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Intl\Languages;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardController extends AbstractDashboardController
{
    public function __construct(private array $enabledLocales)
    {
    }

    #[Route('/dbsaver', name: 'admin')]
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('DbSaver');
    }

    #[Route('/dbsaver/switch-locale/{locale}', name: 'admin_switch_locale')]
    public function switchLocale(Request $request, string $locale): Response
    {
        if (!\in_array($locale, $this->enabledLocales, true)) {
            throw new BadRequestHttpException();
        }

        $request->getSession()->set('_locale', $locale);
        $redirectUrl = $request->headers->get('referer');
        if (empty($redirectUrl) || str_contains($redirectUrl, '/dbsaver/switch-locale')) {
            $redirectUrl = $this->generateUrl('admin');
        }

        return $this->redirect($redirectUrl);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('menu.home', 'fa fa-home');
        yield MenuItem::linkToCrud('menu.databases', 'fas fa-database', Database::class);
        yield MenuItem::linkToCrud('menu.backups', 'fas fa-shield-alt', Backup::class);
        yield MenuItem::linkToCrud('menu.users', 'fas fa-users', User::class)
            ->setPermission(User::ROLE_ADMIN);

        $localeLinks = array_map(static function (string $locale): RouteMenuItem {
            return MenuItem::linkToRoute(ucfirst(Languages::getName($locale, $locale)), null, 'admin_switch_locale', ['locale' => $locale]);
        }, $this->enabledLocales);
        yield MenuItem::subMenu('menu.switch_locale', 'fas fa-language')->setSubItems($localeLinks);
    }
}
