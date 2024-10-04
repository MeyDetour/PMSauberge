<?php

namespace App\DataFixtures;

use App\Entity\Bed;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($produ
        $faker = \Faker\Factory::create();
        $user = new User();
        $user->setEmail("mey");
        $user->setFirstName("Mey");
        $user->setLastName("meymey");
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setProfession("Manager");
        $user->setPassword( '$2y$13$FhkMbbE13NYUtjESrZ3WveSG9x4O0I23VSCJgGFv34skifd/iuu5G');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPhoneNumber("07 82 40 80 49");
        $manager->persist($user);
        $manager->flush();

        $count = 0;
        for ($i = 0; $i < 5; $i++) {
            $room = new Room();
            $room->setName("Room " . $i);
            $room->setPrivate(false);
            $room->setHasLocker(false);
            $room->setHasPrivateShowerroom(false);
            $manager->persist($room);
            $manager->flush();

            for ($k = 0; $k < 3; $k++) {
                $bed = new Bed();
                $bed->setNumber($count);
                $bed->setState("inspected");
                $bed->setDunkBed(false);
                $bed->setSittingApart(false);
                $bed->setRoom($room);
                $manager->persist($bed);
                $count++;
            }
            $manager->flush();
        }
    }
}
