<?php

namespace App\Controller\Admin;

use App\Entity\SiteSettings;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;

/** @extends AbstractCrudController<SiteSettings> */
final class SiteSettingsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SiteSettings::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Réglages')
            ->setEntityLabelInPlural('Réglages du site')
            ->setDefaultRowAction(Action::EDIT);
    }

    public function configureActions(Actions $actions): Actions
    {
        // A single settings row, created by the fixtures
        return $actions->disable(Action::NEW, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield EmailField::new('contactEmail', 'Email de contact')
            ->setHelp('Affiché dans la section Contact.');
        yield ArrayField::new('roles', 'Titres du Hero')
            ->setHelp('Les titres qui défilent en haut de la page, dans l\'ordre.');
    }
}
