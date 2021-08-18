<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Database;
use App\Entity\User;
use App\Service\BackupService;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method User|null getUser()
 */
class DatabaseCrudController extends AbstractCrudController
{
    public function __construct(
        private BackupService $backupService,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Database::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('db_name', 'Nom'))
            ->add(TextFilter::new('host', 'Host'))
            ->add(NumericFilter::new('port', 'Port'))
            ->add(TextFilter::new('db_user', 'Utilisateur'))
            ->add(NumericFilter::new('max_backups', 'Nombre maximal de backups'))
            ->add(DateTimeFilter::new('createdAt', 'Ajouté le'))
            ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb
            ->join('entity.user', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user', $this->getUser()->getId())
            ->orderBy('entity.createdAt', 'DESC');

        return $qb;
    }

    public function launchBackupAction(AdminContext $context): Response
    {
        $database = $context->getEntity()->getInstance();
        $this->backupService->backup($database, 'Backup manuel');
        $this->backupService->clean($database);

        $this->addFlash(
            'success',
            'Le backup a bien été créé !'
        );

        return $this->redirect($context->getReferrer());
    }

    public function showDatabaseBackupsAction(AdminContext $context): Response
    {
        /** @var Database $database */
        $database = $context->getEntity()->getInstance();

        $url = $this->adminUrlGenerator->setController(BackupCrudController::class)
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->set('filters', [
                'db' => [
                    'comparison' => '=',
                    'value' => (string) $database->getId(),
                ],
            ])
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureActions(Actions $actions): Actions
    {
        $launchBackupAction = Action::new('launchBackup', 'Lancer un backup')
            ->linkToCrudAction('launchBackupAction');

        $showDatabaseBackupsAction = Action::new('showDatabaseBackups', 'Voir les backups')
            ->linkToCrudAction('showDatabaseBackupsAction');

        return $actions
            ->add(Crud::PAGE_INDEX, $launchBackupAction)
            ->add(Crud::PAGE_INDEX, $showDatabaseBackupsAction)
            ->setPermission('launchBackup', 'can_show_database')
            ->setPermission('showDatabaseBackups', 'can_show_database')
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter une base de données');
            })
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des bases de données')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier les paramètres de la base de données')
            ->setPageTitle(Crud::PAGE_NEW, 'Nouvelle base de données')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('db_name', 'Nom de la base de données');
        yield TextField::new('host', 'Host');
        yield NumberField::new('port', 'Port');
        yield TextField::new('db_user', 'Utilisateur');
        yield TextField::new('db_plain_password', 'Mot de passe')
            ->setHelp("Les mots de passes seront cryptés et n'apparaîtront pas en
                clair. Ils seront décryptés uniquement lorsqu'ils seront nécessaires pour
                lancer les backups. Vous devrez renseigner ce champs à chaque modification."
            )
            ->onlyOnForms()
            ->setRequired(true);
        yield NumberField::new('max_backups', 'Nombre de backups à mémoriser');

        yield CollectionField::new('backups')
            ->hideOnForm();

        yield DateTimeField::new('createdAt', 'Ajouté le')
            ->setFormat('dd-MM-Y HH:mm')
            ->hideOnForm();
    }
}
