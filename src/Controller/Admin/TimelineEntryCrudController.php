<?php

namespace App\Controller\Admin;

use App\Entity\TimelineEntry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/** @extends AbstractCrudController<TimelineEntry> */
final class TimelineEntryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TimelineEntry::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Étape du parcours')
            ->setEntityLabelInPlural('Parcours')
            ->setDefaultSort(['position' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('position', 'Ordre');
        yield TextField::new('period', 'Période')->setHelp('Ex. : 2022 — Aujourd\'hui');
        yield TextField::new('title', 'Titre')->setHelp('Ex. : Poste · Entreprise');
        yield TextareaField::new('text', 'Description')->hideOnIndex();
    }
}
