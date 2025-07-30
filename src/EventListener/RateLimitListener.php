<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RateLimitListener
{
    private array $paths = [
        '/login',
        '/register',
        '/forgot-password',
        '/reset-password',
    ];

    public function __construct(
        private RateLimiterFactory $loginLimiter
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();
        $method = $request->getMethod();

        if (!in_array($path, $this->paths, true) || $method !== 'POST') {
            return;
        }

        $limiter = $this->loginLimiter->create($request->getClientIp());
        $limit = $limiter->consume(1);
        if (!$limit->isAccepted()) {
            $event->setResponse(new JsonResponse([
                'error' => 'Trop de requêtes. Veuillez réessayer plus tard.'
            ], 429));
        }
    }
}

