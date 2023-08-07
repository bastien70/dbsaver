<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Backup;
use App\Entity\Database;
use App\Entity\FtpAdapter;
use App\Entity\LocalAdapter;
use App\Entity\S3Adapter;
use App\Entity\User;
use App\Helper\LocaleHelper;
use App\Repository\FtpAdapterRepository;
use App\Repository\LocalAdapterRepository;
use App\Repository\S3AdapterRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function __construct(
        private readonly array $enabledLocales,
        private readonly EntityManagerInterface $em,
        private readonly LocalAdapterRepository $localAdapterRepository,
        private readonly S3AdapterRepository $s3AdapterRepository,
        private readonly FtpAdapterRepository $ftpAdapterRepository,
    ) {
    }

    #[Route('/', name: 'admin', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('DbSaver');
    }

    #[Route('/switch-locale/{locale}', name: 'admin_switch_locale', methods: ['GET'])]
    public function switchLocale(Request $request, string $locale): Response
    {
        if (!\in_array($locale, $this->enabledLocales, true)) {
            throw new BadRequestHttpException();
        }

        \assert($this->getUser() instanceof User);
        $this->getUser()->setLocale($locale);
        $this->em->flush();

        $request->getSession()->set('_locale', $locale);
        $redirectUrl = $request->headers->get('referer');
        if (empty($redirectUrl) || str_contains($redirectUrl, 'admin_switch_locale')) {
            $redirectUrl = $this->generateUrl('admin');
        }

        return $this->redirect($redirectUrl);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('menu.home', 'fa fa-home');
        yield MenuItem::subMenu('menu.adapters.name', 'fas fa-bullseye')->setSubItems([
            MenuItem::linkToCrud('menu.adapters.submenu.s3', null, S3Adapter::class)
                ->setBadge($this->s3AdapterRepository->count([])),
            MenuItem::linkToCrud('menu.adapters.submenu.local', null, LocalAdapter::class)
                ->setBadge($this->localAdapterRepository->count([])),
            MenuItem::linkToCrud('menu.adapters.submenu.ftp', null, FtpAdapter::class)
                ->setBadge($this->ftpAdapterRepository->count([])),
        ]);
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
