<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Backup;
use App\Entity\User;
use App\Service\BackupService;
use App\Service\S3Helper;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method User|null getUser()
 */
class BackupCrudController extends AbstractCrudController
{
    public function __construct(
        private S3Helper $s3Helper,
        private BackupService $backupService,
        private int $backupOnLocal
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Backup::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('db', 'Base de données'))
            ->add(DateTimeFilter::new('createdAt', 'Date de création'))
            ->add(ChoiceFilter::new('context', 'Contexte')->setChoices(
                [
                    'Backup quotidien' => 'Backup quotidien',
                    'Backup manuel' => 'Backup manuel',
                ]
            ))
            ->add(TextFilter::new('backupFileName', 'Nom du fichier'));
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb
            ->join('entity.db', 'db')
            ->join('db.user', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user', $this->getUser()->getId())
            ->orderBy('entity.createdAt', 'DESC');

        return $qb;
    }

    /**
     * @throws \Exception
     */
    public function downloadBackupAction(AdminContext $context): Response
    {
        /** @var Backup $backup */
        $backup = $context->getEntity()->getInstance();

        if (true === (bool) $this->backupOnLocal) {
            return $this->backupService->downloadBackupFile($backup);
        }

        return new RedirectResponse($this->s3Helper->generatePresignedUri($backup));
    }

    public function configureActions(Actions $actions): Actions
    {
        $downloadBackupAction = Action::new('downloadBackup', 'Télécharger')
            ->linkToCrudAction('downloadBackupAction');

        return $actions
            ->add(Crud::PAGE_INDEX, $downloadBackupAction)
            ->setPermission('downloadBackup', 'can_show_backup')
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des backups')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('createdAt', 'Créé le')
            ->setFormat('dd-MM-Y HH:mm');
        yield TextField::new('db.db_name', 'Base de données');
        yield TextField::new('context', 'Contexte');
        yield TextField::new('backupFileName', 'Nom du fichier');
    }
}
