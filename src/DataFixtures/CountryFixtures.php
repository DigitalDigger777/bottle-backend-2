<?php
/**
 * Created by PhpStorm.
 * User: korman
 * Date: 29.07.18
 * Time: 0:57
 */

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $country = new Country();
        $country->setName('Россия');
        $manager->persist($country);
        $manager->flush();
    }
}