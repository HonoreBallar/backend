<?php

namespace App\Controller\Api;

use App\Entity\Expertise;
use App\Entity\PipelineStep;
use App\Entity\Project;
use App\Entity\SiteSettings;
use App\Entity\SocialLink;
use App\Entity\Stat;
use App\Entity\Technology;
use App\Entity\TimelineEntry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Returns the whole portfolio content, shaped like frontend/src/lib/data.ts.
 */
final class PortfolioController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Route('/api/portfolio', name: 'api_portfolio', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $settings = $this->em->getRepository(SiteSettings::class)->findOneBy([]);

        return $this->json([
            'contactEmail' => $settings?->getContactEmail(),
            'roles' => $settings?->getRoles() ?? [],
            'stats' => array_map(fn (Stat $s) => [
                'value' => $s->getValue(),
                'label' => $s->getLabel(),
            ], $this->ordered(Stat::class)),
            'stack' => array_map(fn (Technology $t) => [
                'name' => $t->getName(),
                'color' => $t->getColor(),
            ], $this->ordered(Technology::class)),
            'expertise' => array_map(fn (Expertise $e) => [
                'title' => $e->getTitle(),
                'text' => $e->getText(),
                'tags' => $e->getTags(),
                'icon' => $e->getIcon(),
                'color' => $e->getColor(),
            ], $this->ordered(Expertise::class)),
            'projects' => array_map(fn (Project $p) => [
                'title' => $p->getTitle(),
                'description' => $p->getDescription(),
                'category' => $p->getCategory(),
                'tags' => $p->getTags(),
                'glow' => $p->getGlow(),
                'demo' => $p->getDemoUrl(),
                'source' => $p->getSourceUrl(),
            ], $this->ordered(Project::class, ['visible' => true])),
            'pipeline' => array_map(fn (PipelineStep $s) => [
                'n' => $s->getNumber(),
                'title' => $s->getTitle(),
                'text' => $s->getText(),
                'color' => $s->getColor(),
            ], $this->ordered(PipelineStep::class)),
            'timeline' => array_map(fn (TimelineEntry $t) => [
                'period' => $t->getPeriod(),
                'title' => $t->getTitle(),
                'text' => $t->getText(),
            ], $this->ordered(TimelineEntry::class)),
            'socials' => array_map(fn (SocialLink $s) => [
                'label' => $s->getLabel(),
                'href' => $s->getHref(),
            ], $this->ordered(SocialLink::class)),
        ], context: [
            // One badly encoded field must not break the whole site
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS | \JSON_INVALID_UTF8_SUBSTITUTE,
        ]);
    }

    /**
     * @template T of object
     *
     * @param class-string<T>      $class
     * @param array<string, mixed> $criteria
     *
     * @return list<T>
     */
    private function ordered(string $class, array $criteria = []): array
    {
        return $this->em->getRepository($class)->findBy($criteria, ['position' => 'ASC', 'id' => 'ASC']);
    }
}
