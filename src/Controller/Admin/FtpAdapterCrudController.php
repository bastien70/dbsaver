<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\BadgeField;
use App\Entity\FtpAdapter;
use App\Security\Voter\AdapterConfigVoter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class FtpAdapterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FtpAdapter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setPageTitle(Crud::PAGE_INDEX, 'adapter.ftp.index.title')
            ->setPageTitle(Crud::PAGE_NEW, 'adapter.ftp.new.title')
            ->setPageTitle(Crud::PAGE_EDIT, 'adapter.ftp.edit.title')
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

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(TextFilter::new('name', 'adapter.ftp.field.name'))
            ->add(TextFilter::new('prefix', 'adapter.ftp.field.prefix'))
            ->add(TextFilter::new('ftpHost', 'adapter.ftp.field.host'))
            ->add(NumericFilter::new('ftpPort', 'adapter.ftp.field.port'))
            ->add(TextFilter::new('ftpUsername', 'adapter.ftp.field.username'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'adapter.ftp.field.name')
            ->setColumns('col-md-6');
        yield TextField::new('prefix', 'adapter.ftp.field.prefix')
            ->setHelp('adapter.ftp.field.help.prefix')
            ->setColumns('col-md-6');
        yield TextField::new('ftpHost', 'adapter.ftp.field.host')
            ->setColumns(5);
        yield NumberField::new('ftpPort', 'adapter.ftp.field.port')
            ->setColumns(1);
        yield TextField::new('ftpUsername', 'adapter.ftp.field.username')
            ->setColumns(3);
        yield TextField::new('ftpPlainPassword', 'adapter.ftp.field.plainPassword')
            ->setColumns(3)
            ->setHelp('adapter.ftp.field.help.plainPassword')
            ->setRequired(Crud::PAGE_NEW === $pageName)
            ->onlyOnForms();
        yield BooleanField::new('ftpSsl', 'adapter.ftp.field.ssl')
            ->setColumns(12)
            ->onlyOnForms();
        yield BadgeField::new('savesCount', 'adapter.ftp.field.backups')
            ->hideOnForm();
    }
}
