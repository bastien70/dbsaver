<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\BadgeField;
use App\Entity\LocalAdapter;
use App\Security\Voter\AdapterConfigVoter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

final class LocalAdapterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LocalAdapter::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', 'adapter.local.field.name'))
            ->add(TextFilter::new('prefix', 'adapter.local.field.prefix'));
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setPageTitle(Crud::PAGE_INDEX, 'adapter.local.index.title')
            ->setPageTitle(Crud::PAGE_NEW, 'adapter.local.new.title')
            ->setPageTitle(Crud::PAGE_EDIT, 'adapter.local.edit.title')
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

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'adapter.local.field.name');
        yield TextField::new('prefix', 'adapter.local.field.prefix')
            ->setHelp('adapter.local.field.help.prefix');
        yield BadgeField::new('savesCount', 'adapter.local.field.backups')
            ->hideOnForm();
    }
}
