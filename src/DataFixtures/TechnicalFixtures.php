<?php

namespace App\DataFixtures;

use App\Entity\Technical;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TechnicalFixtures extends Fixture
{
    /**
     * @var array|string[]
     */
    private array $technicals = ['acrylique','aérosol','aquarelle','dripping','diptyque','gouache','huile','laque','monochrome'];
    public function load(ObjectManager $manager): void
    {
        foreach ($this->technicals AS $technical) {
            $tech = new Technical();
            $tech->setName($technical);
            $manager->persist($tech);
        }

        $manager->flush();
    }
}
