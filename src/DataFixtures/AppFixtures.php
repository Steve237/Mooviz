<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();

        $category->setCategoryName('Films');
        $category->setImagecategory('https://img.icons8.com/emoji/40/000000/movie-camera-emoji.png');
        
        $manager->persist($category);

        $manager->flush();
    }
}
