<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\BadgeField;
use App\Entity\AdapterConfig;
use App\Entity\Backup;
use App\Entity\Database;
use App\Entity\Embed\BackupTask;
use App\Entity\Enum\BackupTaskPeriodicity;
use App\Entity\User;
use App\Helper\DatabaseHelper;
use App\Service\BackupService;
use App\Service\BackupStatus;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use function sprintf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @method User|null getUser()
 */
final class DatabaseCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly BackupService $backupService,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly DatabaseHelper $databaseHelper,
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
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
            ->add(ChoiceFilter::new('status', 'database.field.status')->setChoices(array_combine(
                array_map(fn (string $status): string => 'database.choices.status.' . $status, Database::getAvailableStatuses()),
                Database::getAvailableStatuses(),
            ))->setFormTypeOption('translation_domain', 'messages'))
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->orderBy('entity.createdAt', 'DESC');
    }

    public function launchBackupAction(AdminContext $context): Response
    {
        $database = $context->getEntity()->getInstance();

        $backupStatus = $this->backupService->backup($database, Backup::CONTEXT_MANUAL);
        $this->backupService->clean($database);

        if (BackupStatus::STATUS_OK === $backupStatus->getStatus()) {
            $this->addFlash('success', new TranslatableMessage('database.launch_backup.flash_success'));
            $status = Database::STATUS_OK;
        } else {
            $this->addFlash('danger', new TranslatableMessage('database.launch_backup.flash_error', ['%message%' => $backupStatus->getErrorMessage()]));
            $status = Database::STATUS_ERROR;
        }

        $this->updateEntity($this->em, $database, $status);

        return $this->redirect($context->getReferrer() ?? $this->generateUrl('admin'));
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

    public function checkConnection(AdminContext $context): Response
    {
        /** @var Database $database */
        $database = $context->getEntity()->getInstance();

        if ($this->databaseHelper->isConnectionOk($database)) {
            $this->addFlash('success', new TranslatableMessage('database.check_connection.flash_success', ['%database%' => $database->getName()]));
            $status = Database::STATUS_OK;
        } else {
            $this->addFlash('danger', new TranslatableMessage('database.check_connection.flash_error', [
                '%database%' => $database->getName(),
                '%error%' => $this->databaseHelper->getLastExceptionMessage(),
            ]));
            $status = Database::STATUS_ERROR;
        }

        $this->updateEntity($this->em, $database, $status);

        $url = $this->adminUrlGenerator->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    /**
     * @param Database $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // When creating a new Database, it is validated so the connection must be ok.
        $entityInstance->setStatus(Database::STATUS_OK);
        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * @param Database $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance, string $status = Database::STATUS_OK): void
    {
        $entityInstance->setStatus($status);
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureActions(Actions $actions): Actions
    {
        $launchBackupAction = Action::new('launchBackup', 'database.action.launch_backup')
            ->linkToCrudAction('launchBackupAction');

        $showDatabaseBackupsAction = Action::new('showDatabaseBackups', 'database.action.show_database_backups')
            ->linkToCrudAction('showDatabaseBackupsAction');

        $checkConnectionAction = Action::new('checkConnection', 'database.action.check_connection')
            ->linkToCrudAction('checkConnection');

        return $actions
            ->add(Crud::PAGE_INDEX, $launchBackupAction)
            ->add(Crud::PAGE_INDEX, $showDatabaseBackupsAction)
            ->add(Crud::PAGE_INDEX, $checkConnectionAction)
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
            ->setFormOptions(['validation_groups' => ['Default', 'Create']], ['validation_groups' => ['Default']])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('database.panel.main_info', 'fas fa-info-circle');
        yield TextField::new('name', 'database.field.name')
            ->hideOnIndex()
            ->setColumns(4);
        yield TextField::new('host', 'database.field.host')
            ->hideOnIndex()
            ->setColumns(4);
        yield NumberField::new('port', 'database.field.port')
            ->hideOnIndex()
            ->setColumns(4);
        yield TextField::new('user', 'database.field.user')
            ->hideOnIndex()
            ->setColumns(6);
        yield TextField::new('displayDsn', 'database.field.dsn')
            ->onlyOnIndex();
        yield TextField::new('plainPassword', 'database.field.password')
            ->setHelp('database.help.password')
            ->onlyOnForms()
            ->setColumns(6)
            ->setRequired(Crud::PAGE_NEW === $pageName);
        yield NumberField::new('maxBackups', 'database.field.max_backups')
            ->hideOnIndex()
            ->setColumns(6);
        yield AssociationField::new('adapter', 'database.field.adapter')
            ->setFormTypeOption('class', AdapterConfig::class)
            ->hideWhenUpdating()
            ->setColumns(6);
        yield BadgeField::new('backups', 'database.field.backups')
            ->formatValue(function ($value) {
                return \count($value);
            })
            ->hideOnForm();

        yield BadgeField::new('backupTask', 'database.field.backup_task.periodicity')
            ->formatValue(function (BackupTask $backupTask) {
                $plural = $backupTask->getPeriodicityNumber() > 1;

                return sprintf(
                    '%s %s %s',
                    $this->translator->trans($backupTask->getDescriptionPrefixTranslation()),
                    $plural ? $backupTask->getPeriodicityNumber() : null,
                    $this->translator->trans($backupTask->getDescriptionSuffixTranslation())
                );
            })
            ->hideOnForm();
        yield BadgeField::new('backupTask.nextIteration', 'database.field.backup_task.next_iteration')
            ->formatValue(function ($value) {
                return $value->format($this->translator->trans('global.date_format'));
            })
            ->hideOnForm();

        yield DateField::new('createdAt', 'database.field.created_at')
            ->setFormat($this->translator->trans('global.easy_admin_date_format'))
            ->hideOnForm();
        yield ChoiceField::new('status', 'database.field.status')
            ->setChoices(array_combine(
                array_map(fn (string $status): string => 'database.choices.status.' . $status, Database::getAvailableStatuses()),
                Database::getAvailableStatuses(),
            ))
            ->renderAsBadges([
                Database::STATUS_OK => 'success',
                Database::STATUS_ERROR => 'danger',
                Database::STATUS_UNKNOWN => 'secondary',
            ])
            ->hideOnForm();

        if (Crud::PAGE_INDEX !== $pageName) {
            yield FormField::addPanel('database.panel.backup_options', 'fas fa-gear');

            yield BooleanField::new('options.resetAutoIncrement', 'database.field.options.reset_auto_increment')
                ->renderAsSwitch(false)
                ->setColumns(6);
            yield BooleanField::new('options.addDropDatabase', 'database.field.options.add_drop_database')
                ->renderAsSwitch(false)
                ->setColumns(6);

            yield FormField::addRow();
            yield BooleanField::new('options.addDropTable', 'database.field.options.add_drop_table')
                ->renderAsSwitch(false)
                ->setColumns(6);
            yield BooleanField::new('options.addDropTrigger', 'database.field.options.add_drop_trigger')
                ->renderAsSwitch(false)
                ->setColumns(6);

            yield FormField::addRow();
            yield BooleanField::new('options.addLocks', 'database.field.options.add_locks')
                ->renderAsSwitch(false)
                ->setColumns(6);
            yield BooleanField::new('options.completeInsert', 'database.field.options.complete_insert')
                ->renderAsSwitch(false)
                ->setColumns(6);
        }

        if (Crud::PAGE_INDEX !== $pageName) {
            yield FormField::addPanel('database.panel.task_configuration', 'fa-solid fa-calendar');
            yield IntegerField::new('backupTask.periodicityNumber', 'database.field.backup_task.periodicity_number')
                ->setFormTypeOption('attr', ['min' => 1, 'step' => 1])
                ->setColumns(4);
            yield ChoiceField::new('backupTask.periodicity', 'database.field.backup_task.periodicity')
                ->setChoices(BackupTaskPeriodicity::cases())
                ->setFormTypeOption('choice_label', function (?BackupTaskPeriodicity $periodicity) {
                    return $this->translator->trans($periodicity?->formLabel());
                })
                ->setFormTypeOption('choice_value', function (?BackupTaskPeriodicity $periodicity) {
                    return $periodicity?->value;
                })
                ->setColumns(4);
            yield DateField::new('backupTask.startFrom', 'database.field.backup_task.start_from')
                ->setFormTypeOption('attr', ['min' => (new DateTime('tomorrow'))->format('Y-m-d')])
                ->setColumns(4);
        }
    }
}
