<?php

namespace App\Service;

use App\Entity\Fighter;
use App\Entity\Reservation;
use App\Repository\FighterRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class FighterService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private CacheInterface $cache,
        private FighterRepository $fighterRepository,
    ) {}

    public function index(Request $request)
    {
        $params = $request->query->all('params');
        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 10;
        $offset = ($page - 1) * $perPage;
        // $totalFighters = $this->fighterRepository->count([]);
        $totalFighters = $this->cache->get('total_fighters', function(ItemInterface $item) {
            $item->expiresAfter(3600);
            return $this->fighterRepository->count([]);
        });
        // $fighters = $this->fighterRepository->findBy([], ['id' => 'DESC'], $perPage, $offset);
        $fighters = $this->cache->get('fighters', function(ItemInterface $item) use ($perPage, $offset) {
            $item->expiresAfter(3600);
            return $this->fighterRepository->findBy([], ['id' => 'DESC'], $perPage, $offset);
        });
        $fightersArray = array_map(fn(Fighter $fighter) => $fighter->toArray(), $fighters);
        return new JsonResponse(['fighters' => $fightersArray, 'totalFighters' => $totalFighters, 'page' => $page, 'perPage' => $perPage]);
    }

    public function show(int $id): JsonResponse
    {
        $fighter = $this->cache->get('fighter_'.$id, function(ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            return $this->fighterRepository->find($id);
        });

        if (!$fighter) {
            throw new NotFoundHttpException(sprintf('No fighter found for id "%d"', $id));
        }

        $reservationDates = array_unique(array_map(
            fn($date) => $date->format('Y-m-d'),
            array_merge(...array_map(
                fn($r) => CarbonPeriod::create($r->getStartAt(), $r->getEndAt())->toArray(),
                $fighter->getReservations()->toArray()
            ))
        ));
        sort($reservationDates);

        return new JsonResponse([
            'fighter' => $fighter->toArray(),
            'reservationDates' => $reservationDates,
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $this->serializer->deserialize(
            $request->getContent(),
            Fighter::class,
            'json'
        );
        $fighter = new Fighter();
        $fighter->setName($data->name);
        $fighter->setNickname($data->nickname);
        $fighter->setDescription($data->description);
        $fighter->setWeightClass($data->weightClass);
        $fighter->setWins($data->wins);
        $fighter->setLosses($data->losses);
        $fighter->setDraws($data->draws);
        $fighter->setCountry($data->country);
        $birthday = \DateTime::createFromFormat('d/m/Y', $data->birthday);
        if ($birthday !== false) {
            $fighter->setBirthday($birthday);
        } else {
            throw new \InvalidArgumentException("Format de date invalide pour 'birthday'. Format attendu : jj/mm/aaaa");
        }
        $fighter->setIsActive($data->isActive);
        !empty($data->height) && $fighter->setHeight($data->height);
        !empty($data->weight) && $fighter->setWeight($data->weight);
        !empty($data->reach) && $fighter->setReach($data->reach);
        !empty($data->stance) && $fighter->setStance($data->stance);
        !empty($data->style) && $fighter->setStyle($data->style);

        $this->entityManager->persist($fighter);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Fighter registered successfully']);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        // $fighter = $this->fighterRepository->find($id);
        $fighter = $this->cache->get('fighter_'.$id, function(ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            return $this->fighterRepository->find($id);
        });
        // dd($fighter);
        if (!$fighter) {
            throw new NotFoundHttpException(sprintf('No fighter found for id "%d"', $id));
        }
        $data = $this->serializer->deserialize($request->getContent(), Fighter::class, 'json');
        $fighter->setName($data->getName());
        $fighter->setNickname($data->getNickname());
        $fighter->setDescription($data->getDescription());
        $fighter->setWeightClass($data->getWeightClass());
        $fighter->setWins($data->getWins());
        $fighter->setLosses($data->getLosses());
        $fighter->setDraws($data->getDraws());
        $fighter->setCountry($data->getCountry());
        $fighter->setBirthday($data->getBirthday());
        $fighter->setIsActive($data->isActive());
        $fighter->setHeight($data->getHeight());
        $fighter->setWeight($data->getWeight());
        $fighter->setReach($data->getReach());
        $fighter->setStance($data->getStance());
        $fighter->setStyle($data->getStyle());
        $fighter->setPlaceOfBirth($data->getPlaceOfBirth());
        $fighter->setPricePerTraining($data->getPricePerTraining());
        $this->cache->delete('fighter_'.$id);
        $this->cache->delete('fighters');
        return new JsonResponse(['message' => 'Fighter updated successfully']);
    }
}
