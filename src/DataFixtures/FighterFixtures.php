<?php

namespace App\DataFixtures;

use App\Entity\Fighter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FighterFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $fighter = new Fighter();
            $fighter->setName('Fighter ' . $i);
            $fighter->setNickname('Fighter ' . $i);
            $fighter->setDescription('Description ' . $i);
            $fighter->setWeightClass('Weight Class ' . $i);
            $fighter->setWins(0);
            $fighter->setLosses(0);
            $fighter->setDraws(0);
            $fighter->setCountry('Country ' . $i);
            $fighter->setBirthday(\DateTime::createFromFormat('d/m/Y', '01/01/2000'));
            $fighter->setPlaceOfBirth('Place Of Birth ' . $i);
            $fighter->setNoContest(0);
            $fighter->setIsActive(true);
            $fighter->setHeight('Height ' . $i);
            $fighter->setWeight('Weight ' . $i);
                    $fighter->setReach('Reach ' . $i);
            $fighter->setPricePerTraining(random_int(100, 10000));
            $fighter->setStance('Stance ' . $i);
            $fighter->setStyle('Style ' . $i);

            $manager->persist($fighter);

        }
        $manager->flush();
    }
}
