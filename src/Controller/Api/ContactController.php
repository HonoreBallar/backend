<?php

namespace App\Controller\Api;

use App\Dto\ContactRequest;
use App\Entity\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/api/contact', name: 'api_contact', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] ContactRequest $contact,
        Request $request,
        EntityManagerInterface $em,
        RateLimiterFactoryInterface $contactLimiter,
    ): JsonResponse {
        // Bots filling the honeypot get a fake success
        if ('' !== $contact->website) {
            return $this->json(['status' => 'sent'], Response::HTTP_CREATED);
        }

        $limit = $contactLimiter->create($request->getClientIp())->consume();
        if (!$limit->isAccepted()) {
            return $this->json(
                ['error' => 'Trop de messages envoyés, réessayez plus tard.'],
                Response::HTTP_TOO_MANY_REQUESTS,
                ['Retry-After' => $limit->getRetryAfter()->getTimestamp() - time()],
            );
        }

        $em->persist(new ContactMessage(trim($contact->name), trim($contact->email), trim($contact->message)));
        $em->flush();

        return $this->json(['status' => 'sent'], Response::HTTP_CREATED);
    }
}
