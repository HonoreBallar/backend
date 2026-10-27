<?php

namespace App\Controller\Admin;

use App\Entity\Expertise;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/** @extends AbstractCrudController<Expertise> */
final class ExpertiseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Expertise::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Expertise')
            ->setEntityLabelInPlural('Expertises')
            ->setDefaultSort(['position' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('position', 'Ordre');
        yield TextField::new('title', 'Titre');
        yield TextareaField::new('text', 'Texte')->hideOnIndex();
        yield ArrayField::new('tags', 'Tags');
        yield ChoiceField::new('icon', 'Icône')
            ->setChoices(['Code' => 'code', 'Puce (IA)' => 'chip', 'Nuage' => 'cloud']);
        yield ColorField::new('color', 'Couleur')->showValue();
    }
}
