<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{

    private array $categories = ['contemporain','baroque','bauhaus','biedermeier','expressionnisme','futurisme','gothique','urbain','classicisme','maniérisme'];

    public function load(ObjectManager $manager): void
    {

        foreach ($this->categories AS $category) {
            $cat = new Category();
            $cat->setName($category);
            $manager->persist($cat);
        }

        $manager->flush();
    }
}
