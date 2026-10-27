<?php

namespace App\Controller\Admin;

use App\Entity\SocialLink;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/** @extends AbstractCrudController<SocialLink> */
final class SocialLinkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SocialLink::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Réseau social')
            ->setEntityLabelInPlural('Réseaux sociaux')
            ->setDefaultSort(['position' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('position', 'Ordre');
        yield TextField::new('label', 'Nom');
        yield TextField::new('href', 'Lien');
    }
}
