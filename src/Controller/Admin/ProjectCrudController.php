<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/** @extends AbstractCrudController<Project> */
final class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Projet')
            ->setEntityLabelInPlural('Projets')
            ->setDefaultSort(['position' => 'ASC'])
            ->setSearchFields(['title', 'description', 'category']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('position', 'Ordre');
        yield TextField::new('title', 'Titre');
        yield ChoiceField::new('category', 'Catégorie')
            ->setChoices(array_combine(Project::CATEGORIES, Project::CATEGORIES))
            ->renderAsBadges(['Fullstack' => 'info', 'IA' => 'primary', 'DevOps' => 'success']);
        yield BooleanField::new('visible', 'Visible')->renderAsSwitch();
        yield TextareaField::new('description', 'Description')->hideOnIndex();
        yield ArrayField::new('tags', 'Tags')->hideOnIndex();
        yield TextField::new('glow', 'Couleur du halo')
            ->setHelp('Couleur RGB au format "r, g, b", ex. 139, 92, 246')
            ->hideOnIndex();
        yield TextField::new('demoUrl', 'Lien démo')->setHelp('Laisser vide pour "#".')->hideOnIndex();
        yield TextField::new('sourceUrl', 'Lien code source')->setHelp('Laisser vide pour "#".')->hideOnIndex();
    }
}
