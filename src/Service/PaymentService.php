<?php

namespace App\Service;

use App\DTO\CheckoutData;
use App\Entity\Fighter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\ReservationService;
use Symfony\Component\Serializer\SerializerInterface;

class PaymentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ReservationService $reservationService,
        private SerializerInterface $serializer,
    ) {}

    public function checkout(Request $request, string $hostFront, string $stripeSecretKey)
    {
        $data = $this->serializer->deserialize($request->getContent(), CheckoutData::class, 'json');
        $amount = (int) $data->totalPrice*100;
        $fighter = $this->entityManager->getRepository(Fighter::class)->find($data->fighterId);

        $stripe = new \Stripe\StripeClient($stripeSecretKey);

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Fighter Training Session with ' . $fighter->getName()],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'success_url' => $hostFront . '/payment-success',
            'cancel_url'  => $hostFront . '/payment-failed',
            'metadata' => [
                'fighter_id' => $data->fighterId ?? null,
                'user_id'    => null,
            ],
        ]);
        if($session->success_url) {
            $this->reservationService->createReservation($request);
        }

        // Return the URL (or the ID if you prefer redirectToCheckout on the front)
        return new JsonResponse(['url' => $session->url, 'id' => $session->id]);
    }
}
