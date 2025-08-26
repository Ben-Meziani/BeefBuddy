<?php

namespace App\Controller;

use App\Entity\Fighter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ReservationService;

final class PaymentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ReservationService $reservationService,
    ) {}

    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    #[Route('/checkout', name: 'api_checkout', methods: ['POST'])]
    public function checkout(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // totalPrice doit être en centimes (int). Si tu reçois en euros -> cast*100
        $amount = (int) $data['totalPrice']*100;
        $fighter = $this->entityManager->getRepository(Fighter::class)->find($data['fighterId']);

        $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);

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
            'success_url' => $this->generateUrl('app_success', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'  => $this->generateUrl('app_cancel',  [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
            'metadata' => [
                'fighter_id' => $data['fighterId'] ?? null,
                'user_id'    => null,
            ],
        ]);
        if($session->success_url) {
            $this->reservationService->createReservation($request);
        }

        // Renvoie l’URL (ou l’ID si tu préfères redirectToCheckout côté front)
        return new JsonResponse(['url' => $session->url, 'id' => $session->id]);
    }


    #[Route('/success', name: 'app_success')]
    public function success()
    {
        return $this->render('payment/success.html.twig');
    }

    #[Route('/cancel', name: 'app_cancel')]
    public function cancel()
    {
        return $this->render('payment/cancel.html.twig');
    }
}
