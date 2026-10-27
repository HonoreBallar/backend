<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
final class DashboardController extends AbstractDashboardController
{
    public function __construct(
        #[Autowire(env: 'FRONTEND_URL')] private readonly string $frontendUrl,
    ) {
    }

    public function index(): Response
    {
        return $this->redirectToRoute('admin_project_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Portfolio · Administration')
            ->setLocales(['fr']);
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateTimeFormat('dd/MM/yyyy HH:mm')
            ->showEntityActionsInlined();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Voir le site', 'fa fa-arrow-up-right-from-square', $this->frontendUrl)
            ->setLinkTarget('_blank');

        yield MenuItem::section('Contenu');
        yield MenuItem::linkTo(SiteSettingsCrudController::class, 'Réglages & Hero', 'fa fa-sliders');
        yield MenuItem::linkTo(StatCrudController::class, 'Chiffres clés', 'fa fa-chart-simple');
        yield MenuItem::linkTo(TechnologyCrudController::class, 'Stack technique', 'fa fa-layer-group');
        yield MenuItem::linkTo(ExpertiseCrudController::class, 'Expertises', 'fa fa-lightbulb');
        yield MenuItem::linkTo(ProjectCrudController::class, 'Projets', 'fa fa-folder-open');
        yield MenuItem::linkTo(PipelineStepCrudController::class, 'Pipeline DevOps', 'fa fa-diagram-next');
        yield MenuItem::linkTo(TimelineEntryCrudController::class, 'Parcours', 'fa fa-timeline');
        yield MenuItem::linkTo(SocialLinkCrudController::class, 'Réseaux sociaux', 'fa fa-share-nodes');

        yield MenuItem::section('Contact');
        yield MenuItem::linkTo(ContactMessageCrudController::class, 'Messages reçus', 'fa fa-envelope');
    }
}
