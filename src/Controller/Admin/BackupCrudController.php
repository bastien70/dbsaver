<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\AdapterConfig;
use App\Entity\Backup;
use App\Entity\User;
use App\Helper\FlysystemHelper;
use App\Service\BackupService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @method User|null getUser()
 */
final class BackupCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly FlysystemHelper $flysystemHelper,
        private readonly BackupService $backupService,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Backup::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('database', 'backup.field.database'))
            ->add(DateTimeFilter::new('createdAt', 'backup.field.created_at'))
            ->add(ChoiceFilter::new('context', 'backup.field.context')->setChoices(
                [
                    'backup.choices.context.manual' => Backup::CONTEXT_MANUAL,
                    'backup.choices.context.automatic' => Backup::CONTEXT_AUTOMATIC,
                ]
            )->setFormTypeOption('translation_domain', 'messages'))
            ->add(TextFilter::new('backupFileName', 'backup.field.filename'));
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->orderBy('entity.createdAt', 'DESC');
    }

    public function downloadBackupAction(AdminContext $context): Response
    {
        /** @var Backup $backup */
        $backup = $context->getEntity()->getInstance();

        return $this->flysystemHelper->download($backup);
    }

    public function importBackupAction(AdminContext $context): Response
    {
        /** @var Backup $backup */
        $backup = $context->getEntity()->getInstance();

        try {
            $this->backupService->import($backup);
            $this->addFlash('success', new TranslatableMessage('backup.action.import.flash_success'));
        } catch (\RuntimeException $e) {
            $this->addFlash('danger', new TranslatableMessage('backup.action.import.flash_error', ['%message%' => $e->getMessage()]));
        }

        $url = $this->adminUrlGenerator->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    /**
     * @param Backup $entityInstance
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->flysystemHelper->remove($entityInstance);
        parent::deleteEntity($entityManager, $entityInstance);
    }

    public function configureActions(Actions $actions): Actions
    {
        $downloadBackupAction = Action::new('downloadBackup', 'backup.action.download')
            ->linkToCrudAction('downloadBackupAction');

        $importBackupAction = Action::new('importBackup', 'backup.action.import.title')
            ->linkToCrudAction('importBackupAction')
            ->addCssClass('confirm-action')
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-confirm',
            ]);

        return $actions
            ->add(Crud::PAGE_INDEX, $downloadBackupAction)
            ->add(Crud::PAGE_INDEX, $importBackupAction)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->disable(Action::NEW, Action::EDIT)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'backup.index.title')
            ->setEntityLabelInPlural('backup.admin_label.plural')
            ->setEntityLabelInSingular('backup.admin_label.singular')
        ;
    }

    public function configureAssets(Assets $assets): Assets
    {
        $assets->addJsFile('assets/js/confirm-modal.js');

        return parent::configureAssets($assets);
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('createdAt', 'backup.field.created_at')
            ->setFormat('dd-MM-Y HH:mm');
        yield TextField::new('database.name', 'backup.field.database');
        yield TextField::new('context', 'backup.field.context')->formatValue(function (string $context): string {
            return $this->translator->trans('backup.choices.context.' . $context);
        });
        yield TextField::new('backupFileName', 'backup.field.filename');
        yield AssociationField::new('database.adapter', 'backup.field.adapter')
            ->setFieldFqcn(AdapterConfig::class);
    }

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            TranslatorInterface::class => '?' . TranslatorInterface::class,
        ];
    }
}
