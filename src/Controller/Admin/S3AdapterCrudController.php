<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\BadgeField;
use App\Entity\Enum\S3Provider;
use App\Entity\Enum\S3StorageClass;
use App\Entity\S3Adapter;
use App\Security\Voter\AdapterConfigVoter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class S3AdapterCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return S3Adapter::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', 'adapter.s3.field.name'))
            ->add(TextFilter::new('prefix', 'adapter.s3.field.prefix'))
            ->add(TextFilter::new('s3BucketName', 'adapter.s3.field.bucket_name'))
            ->add(ChoiceFilter::new('s3Provider', 'adapter.s3.field.provider')
                ->setChoices(array_combine(
                    array_map(fn (S3Provider $s3Provider): string => $s3Provider->getText(), S3Provider::cases()),
                    S3Provider::cases(),
                ))
                ->setFormTypeOption('translation_domain', 'messages')
            )
            ->add(TextFilter::new('s3Region', 'adapter.s3.field.region'))
            ->add(TextFilter::new('s3Endpoint', 'adapter.s3.field.endpoint'))
            ->add(ChoiceFilter::new('storageClass', 'adapter.s3.field.storage_class')
                ->setChoices(array_combine(
                    array_map(fn (S3StorageClass $s3StorageClass): string => $s3StorageClass->value, S3StorageClass::cases()),
                    S3StorageClass::cases(),
                ))
            );
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setPageTitle(Crud::PAGE_INDEX, 'adapter.s3.index.title')
            ->setPageTitle(Crud::PAGE_NEW, 'adapter.s3.new.title')
            ->setPageTitle(Crud::PAGE_EDIT, 'adapter.s3.edit.title')
            ->overrideTemplate('crud/new', 'admin/page/adapter/new.html.twig')
            ->overrideTemplate('crud/edit', 'admin/page/adapter/edit.html.twig')
            ->setEntityLabelInPlural('adapter.admin_label.plural')
            ->setEntityLabelInSingular('adapter.admin_label.singular');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('adapter.action.new');
            })
            ->setPermission(Action::EDIT, AdapterConfigVoter::CAN_EDIT_OR_REMOVE_ADAPTER)
            ->setPermission(Action::DELETE, AdapterConfigVoter::CAN_EDIT_OR_REMOVE_ADAPTER)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);
    }

    /**
     * @throws \JsonException
     */
    public function createNewForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        $request = $context->getRequest();
        $formOptions->set('validation_groups', $request->isXmlHttpRequest() ? [] : ['Default', 'Submit', 'Create']);
        $builder = $this->createNewFormBuilder($entityDto, $formOptions, $context);
        $builder = $this->formBuilderModifier($builder, $request);

        return $builder->getForm();
    }

    public function createEditForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        $request = $context->getRequest();
        $formOptions->set('validation_groups', $request->isXmlHttpRequest() ? [] : ['Default', 'Submit']);
        $builder = $this->createEditFormBuilder($entityDto, $formOptions, $context);
        $builder = $this->formBuilderModifier($builder, $request);

        return $builder->getForm();
    }

    public function formBuilderModifier(FormBuilderInterface $builder, Request $request): FormBuilderInterface
    {
        $builder->get('s3Provider')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $provider = $form->getData();
                $this->addS3StorageClassField($form->getParent(), $provider);
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                /** @var S3Adapter $data */
                $data = $event->getData();

                $form = $event->getForm();
                $form->get('storageClass')->setData($data->getStorageClass());
                $this->addS3StorageClassField($form, $data->getS3Provider());
            }
        );

        return $builder;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'adapter.s3.field.name')
            ->setColumns('col-md-6');
        yield TextField::new('prefix', 'adapter.s3.field.prefix')
            ->setColumns('col-md-6');
        yield TextField::new('s3AccessId', 'adapter.s3.field.access_id')
            ->setColumns('col-md-4')
            ->hideOnIndex();
        yield TextField::new('s3PlainAccessSecret', 'adapter.s3.field.access_secret')
            ->setColumns('col-md-4')
            ->hideOnIndex()
            ->setHelp('adapter.s3.field.help.access_secret')
            ->setRequired(Crud::PAGE_NEW === $pageName);
        yield TextField::new('s3BucketName', 'adapter.s3.field.bucket_name')
            ->setColumns('col-md-4');

        yield ChoiceField::new('s3Provider', 'adapter.s3.field.provider')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'choice_value' => fn (?S3Provider $s3Provider): ?string => $s3Provider?->value,
                'choice_label' => fn (S3Provider $s3Provider): string => $this->translator->trans($s3Provider->getText()),
                'class' => S3Provider::class,
                'choices' => S3Provider::cases(),
            ])
            ->onlyOnForms()
            ->renderAsNativeWidget(false)
            ->setRequired(true)
            ->setColumns('col-md-4');

        yield BadgeField::new('s3Provider.text', 'adapter.s3.field.provider')
            ->formatValue(function ($value) {
                return $this->translator->trans($value);
            })
            ->hideOnForm();

        yield TextField::new('s3Region', 'adapter.s3.field.region')
            ->setColumns('col-md-4');

        yield TextField::new('s3Endpoint', 'adapter.s3.field.endpoint')
            ->formatValue(function (?string $value) {
                return $value ?: $this->translator->trans('adapter.s3.field.endpoint_default');
            })
            ->setHelp('adapter.s3.field.help.endpoint')
            ->setColumns('col-md-4');

        yield ChoiceField::new('storageClass', 'adapter.s3.field.storage_class')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'choice_value' => fn (?S3StorageClass $storageClass): ?string => $storageClass?->value,
                'choice_label' => fn (S3StorageClass $storageClass): string => $storageClass->value,
                'class' => S3StorageClass::class,
                'choices' => S3StorageClass::cases(),
            ])
            ->setRequired(false)
            ->onlyOnForms();

        yield ChoiceField::new('storageClass', 'adapter.s3.field.storage_class')
            ->formatValue(static fn ($item, S3Adapter $adapter): ?string => $adapter->getStorageClass()?->value)
            ->hideOnForm();

        yield BadgeField::new('savesCount', 'adapter.s3.field.backups')
            ->hideOnForm();
    }

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            TranslatorInterface::class => '?' . TranslatorInterface::class,
        ];
    }

    private function addS3StorageClassField(FormInterface $form, ?S3Provider $s3Provider): void
    {
        if (null === $s3Provider) {
            $storageClasses = [];
        } else {
            $storageClasses = match ($s3Provider) {
                S3Provider::AMAZON_AWS => S3StorageClass::getAwsStorageClasses(),
                S3Provider::SCALEWAY => S3StorageClass::getScalewayStorageClasses(),
                default => [],
            };
        }

        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'storageClass',
            ChoiceType::class,
            null,
            [
                'label' => 'adapter.s3.field.storage_class',
                'placeholder' => '',
                'choices' => $storageClasses,
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'auto_initialize' => false,
                'choice_label' => function (S3StorageClass $s3StorageClass) {
                    return $s3StorageClass->value;
                },
                'choice_value' => function (?S3StorageClass $s3Provider) {
                    return $s3Provider?->value;
                },
                'help' => 'adapter.s3.field.help.storage_class',
            ]
        );

        $form->add($builder->getForm());
    }
}
