<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Backup;
use App\Entity\Database;
use App\Entity\User;
use App\Security\Voter\DatabaseVoter;
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
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @method User|null getUser()
 */
final class DatabaseCrudController extends AbstractCrudController
{
    public function __construct(
        private BackupService $backupService,
        private AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Database::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', 'database.field.name'))
            ->add(TextFilter::new('host', 'database.field.host'))
            ->add(NumericFilter::new('port', 'database.field.port'))
            ->add(TextFilter::new('user', 'database.field.user'))
            ->add(NumericFilter::new('maxBackups', 'database.field.max_backups'))
            ->add(DateTimeFilter::new('createdAt', 'database.field.created_at'))
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->join('entity.owner', 'owner')
            ->andWhere('owner.id = :user')
            ->setParameter('user', $this->getUser()->getId())
            ->orderBy('entity.createdAt', 'DESC');
    }

    public function launchBackupAction(AdminContext $context): Response
    {
        $database = $context->getEntity()->getInstance();

        try {
            $this->backupService->backup($database, Backup::CONTEXT_MANUAL);
            $this->backupService->clean($database);

            $this->addFlash('success', new TranslatableMessage('database.launch_backup.flash_success'));
        } catch (\Exception $e) {
            $this->addFlash('danger', new TranslatableMessage('database.launch_backup.flash_error', ['%message%' => $e->getMessage()]));
        }

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
                'database' => [
                    'comparison' => '=',
                    'value' => (string) $database->getId(),
                ],
            ])
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureActions(Actions $actions): Actions
    {
        $launchBackupAction = Action::new('launchBackup', 'database.action.launch_backup')
            ->linkToCrudAction('launchBackupAction');

        $showDatabaseBackupsAction = Action::new('showDatabaseBackups', 'database.action.show_database_backups')
            ->linkToCrudAction('showDatabaseBackupsAction');

        return $actions
            ->add(Crud::PAGE_INDEX, $launchBackupAction)
            ->add(Crud::PAGE_INDEX, $showDatabaseBackupsAction)
            ->setPermission('launchBackup', DatabaseVoter::CAN_SHOW_DATABASE)
            ->setPermission('showDatabaseBackups', DatabaseVoter::CAN_SHOW_DATABASE)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('database.action.new');
            })
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'database.index.title')
            ->setPageTitle(Crud::PAGE_EDIT, 'database.edit.title')
            ->setPageTitle(Crud::PAGE_NEW, 'database.new.title')
            ->setEntityLabelInPlural('database.admin_label.plural')
            ->setEntityLabelInSingular('database.admin_label.singular')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'database.field.name');
        yield TextField::new('host', 'database.field.host');
        yield NumberField::new('port', 'database.field.port');
        yield TextField::new('user', 'database.field.user');
        yield TextField::new('plainPassword', 'database.field.password')
            ->setHelp('database.help.password')
            ->onlyOnForms()
            ->setRequired(true);
        yield NumberField::new('maxBackups', 'database.field.max_backups');

        yield CollectionField::new('backups', 'database.field.backups')
            ->hideOnForm();

        yield DateTimeField::new('createdAt', 'database.field.created_at')
            ->setFormat('dd-MM-Y HH:mm')
            ->hideOnForm();
    }
}
