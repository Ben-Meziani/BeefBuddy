<?php

namespace App\Controller;

use App\Entity\Fighter;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ReservationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
    ) {}

    #[Route('/reservation', name: 'app_reservation', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $reservation = new Reservation();
        $fighter = $this->entityManager->getRepository(Fighter::class)->find($data['fighterId']);

        $user = $this->entityManager->getRepository(User::class)->find($data['userId']);
        $reservation->setFighter($fighter);
        $reservation->setUser($user);
        $reservation->setTotalPrice($data['totalPrice']);
        $reservation->setStartAt(new \DateTimeImmutable($data['dates'][0]));
        $reservation->setEndAt(new \DateTimeImmutable($data['dates'][0] ?? $data['dates'][1]));

        // Valider l'entité avant de la persister
        $errors = $this->validator->validate($reservation);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Reservation created']);
    }
}
