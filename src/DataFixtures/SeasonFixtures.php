<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SeasonFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i=0; $i < 500; $i++) {
            $serie = new Serie();
            $serie->setName($faker->words(7, true))
                ->setGenres($faker->randomElement(['SF', 'Western', 'Comedy', 'Drama', 'Thriller', 'Gore']))
                ->setFirstAirDate($faker->dateTimeBetween(new \DateTime('-10 year'), new \DateTime('-1 month')))
                ->setStatus($faker->randomElement(['Ended', 'Returning', 'Canceled']))
                ->setVote($faker->randomFloat(1, 0, 10))
                ->setPopularity($faker->randomFloat(2, 0, 1000))
                ->setDateCreated(new \DateTime())
            ;

            if (\in_array($serie->getStatus(), ['Ended', 'Canceled'])) {
                $serie->setLastAirDate($faker->dateTimeBetween($serie->getFirstAirDate(), new \DateTime()));
            }

            $manager->persist($serie);
        }

        $manager->flush();
    }
}
