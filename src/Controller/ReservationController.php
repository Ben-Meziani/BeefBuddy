<?php

namespace App\Controller;

use App\Entity\Fighter;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\ReservationService;

final class ReservationController extends AbstractController
{
    public function __construct(
        private ReservationService $reservationService,
    ) {}

    #[Route('/reservation/user/{id}', name: 'app_user_reservation', methods: ['GET'])]
    public function getUserReservation(int $id, Request $request): JsonResponse
    {
        try {
            return $this->reservationService->getUserReservation($id, $request);
        }
        catch(\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
