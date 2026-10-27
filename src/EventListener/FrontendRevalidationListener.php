<?php

namespace App\EventListener;

use App\Entity\PortfolioContent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Asks the Next.js frontend to refresh its cached portfolio content
 * whenever an admin change touches a PortfolioContent entity.
 */
#[AsDoctrineListener(event: Events::onFlush)]
#[AsDoctrineListener(event: Events::postFlush)]
final class FrontendRevalidationListener
{
    private bool $contentChanged = false;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        #[Autowire(env: 'FRONTEND_URL')] private readonly string $frontendUrl,
        #[Autowire(env: 'REVALIDATE_SECRET')] private readonly string $secret,
    ) {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getObjectManager()->getUnitOfWork();
        $changed = [
            ...$uow->getScheduledEntityInsertions(),
            ...$uow->getScheduledEntityUpdates(),
            ...$uow->getScheduledEntityDeletions(),
        ];

        foreach ($changed as $entity) {
            if ($entity instanceof PortfolioContent) {
                $this->contentChanged = true;

                return;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (!$this->contentChanged || '' === $this->secret) {
            return;
        }
        $this->contentChanged = false;

        // A frontend that is down must never prevent saving in the admin
        try {
            $response = $this->httpClient->request('POST', rtrim($this->frontendUrl, '/').'/api/revalidate', [
                'headers' => ['x-revalidate-secret' => $this->secret],
                'timeout' => 3,
            ]);
            if (200 !== $response->getStatusCode()) {
                $this->logger->warning('Frontend revalidation returned HTTP {status}.', ['status' => $response->getStatusCode()]);
            }
        } catch (\Throwable $e) {
            $this->logger->warning('Frontend revalidation failed: {message}', ['message' => $e->getMessage()]);
        }
    }
}
