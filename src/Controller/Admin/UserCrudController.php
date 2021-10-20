<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\BadgeField;
use App\Entity\User;
use App\Helper\LocaleHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    /**
     * @param array<string> $enabledLocales
     */
    public function __construct(private array $enabledLocales)
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'user.index.title')
            ->setPageTitle(Crud::PAGE_EDIT, 'user.edit.title')
            ->setPageTitle(Crud::PAGE_NEW, 'user.new.title')
            ->setEntityLabelInPlural('user.admin_label.plural')
            ->setEntityLabelInSingular('user.admin_label.singular')
            ->setFormOptions(['validation_groups' => ['Default', 'Create']], ['validation_groups' => ['Default']])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('user.action.new');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setLabel('user.action.edit');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setLabel('user.action.delete');
            })
            ->update(Crud::PAGE_INDEX, Action::BATCH_DELETE, function (Action $action) {
                return $action->setLabel('user.action.delete');
            })
            ->setPermission(Action::NEW, User::ROLE_ADMIN)
            ->setPermission(Action::DELETE, User::ROLE_ADMIN)
            ->setPermission(Action::BATCH_DELETE, User::ROLE_ADMIN)
            ->setPermission(Action::EDIT, User::ROLE_ADMIN)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_EDIT, Action::INDEX);
    }

    public function configureFields(string $pageName): iterable
    {
        yield EmailField::new('email', 'user.field.email');
        yield TextField::new('plainPassword', 'user.field.password')
            ->onlyOnForms()
            ->setRequired(Crud::PAGE_NEW === $pageName);
        yield ChoiceField::new('role', 'user.field.role')
            ->setChoices(array_combine(array_map(fn (string $role): string => 'user.choices.role.' . $role, User::getAvailableRoles()), User::getAvailableRoles()));
        yield ChoiceField::new('locale', 'user.field.locale')
            ->setChoices(array_combine(array_map(fn (string $locale): string => LocaleHelper::getLanguageName($locale), $this->enabledLocales), $this->enabledLocales));
        yield BadgeField::new('databases', 'user.field.databases')
            ->formatValue(function ($value) {
                return \count($value);
            })
            ->hideOnForm();
    }
}
