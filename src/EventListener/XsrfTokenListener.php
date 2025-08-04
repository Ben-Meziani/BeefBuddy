<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class XsrfTokenListener
{
    public function __construct(private JWTEncoderInterface $jwtEncoder) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (str_starts_with($request->getPathInfo(), '/api')) {
            $accessToken = $request->cookies->get('access_token');
            $xsrfHeader = $request->headers->get('X-XSRF-TOKEN');

            if (!$accessToken || !$xsrfHeader) {
                $event->setResponse(new JsonResponse(['error' => 'Missing token'], 403));
                return;
            }

            try {
                $payload = $this->jwtEncoder->decode($accessToken);
                if (($payload['xsrfToken'] ?? null) !== $xsrfHeader) {
                    $event->setResponse(new JsonResponse(['error' => 'Invalid XSRF token'], 403));
                }
            } catch (\Exception $e) {
                $event->setResponse(new JsonResponse(['error' => 'Invalid JWT'], 403));
            }
        }
    }
}
