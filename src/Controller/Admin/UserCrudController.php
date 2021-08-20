<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'user.index.title')
            ->setEntityLabelInPlural('user.admin_label.plural')
            ->setEntityLabelInSingular('user.admin_label.singular')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield EmailField::new('email', 'user.field.email');
        yield TextField::new('password', 'user.field.password')
            ->onlyOnForms();
        yield ChoiceField::new('roles', 'user.field.roles')
            ->setChoices([
                'user.choices.role.user' => 'ROLE_USER',
                'user.choices.roles.admin' => 'ROLE_ADMIN'
            ]);
    }

}
