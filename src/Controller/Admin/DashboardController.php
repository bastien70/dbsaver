<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Backup;
use App\Entity\Database;
use App\Entity\User;
use App\Helper\LocaleHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\RouteMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

final class DashboardController extends AbstractDashboardController
{
    /**
     * @param array<string> $enabledLocales
     */
    public function __construct(private array $enabledLocales)
    {
    }

    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('DbSaver');
    }

    #[Route('/switch-locale/{locale}', name: 'admin_switch_locale')]
    public function switchLocale(Request $request, string $locale): Response
    {
        if (!\in_array($locale, $this->enabledLocales, true)) {
            throw new BadRequestHttpException();
        }

        $this->getUser()->setLocale($locale);
        $this->getDoctrine()->getManager()->flush();

        $request->getSession()->set('_locale', $locale);
        $redirectUrl = $request->headers->get('referer');
        if (empty($redirectUrl) || str_contains($redirectUrl, '/switch-locale')) {
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
            return MenuItem::linkToRoute(LocaleHelper::getLanguageName($locale), null, 'admin_switch_locale', ['locale' => $locale]);
        }, $this->enabledLocales);
        yield MenuItem::subMenu('menu.switch_locale', 'fas fa-language')->setSubItems($localeLinks);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->addMenuItems([
                MenuItem::linkToRoute('menu.settings', 'fas fa-user-cog', 'app_user_settings'),
            ]);
    }
}
