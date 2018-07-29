<?php
/**
 * Created by PhpStorm.
 * User: korman
 * Date: 29.07.18
 * Time: 0:57
 */

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setPhone('+380991576192');
        $user->setPassword(hash('sha256','1demo!'));
        $user->setVerificationCode(rand(1111, 9999));
        $user->setIsVerify(true);

        $manager->persist($user);
        $manager->flush();
    }
}