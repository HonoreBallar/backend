<?php

namespace App\Controller\Admin;

use App\Entity\PipelineStep;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/** @extends AbstractCrudController<PipelineStep> */
final class PipelineStepCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PipelineStep::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Étape')
            ->setEntityLabelInPlural('Pipeline DevOps')
            ->setDefaultSort(['position' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('position', 'Ordre');
        yield TextField::new('number', 'Numéro')->setHelp('Ex. : 01');
        yield TextField::new('title', 'Titre');
        yield TextField::new('text', 'Texte');
        yield ColorField::new('color', 'Couleur')->showValue();
    }
}
