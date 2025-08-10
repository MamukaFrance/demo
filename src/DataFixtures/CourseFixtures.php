<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Course;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CourseFixtures extends Fixture
{
    // Rappel: Injecter dans un constructeur = est accessible dans toute la classe
    // Ca devient implicitement un membre de classe
    public function __construct(private UserPasswordHasherInterface $passwordHasher){
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $course = new Course();
        $course->setName('PHP');
        $course->setContent('Le développment coté serveur PHP');
        $course->setDuration(10);
        $course->setPublished(true);
        $course->setDateCreated(new \DateTimeImmutable());

        $manager->persist($course);

        for ($i = 0; $i < 30; $i++) {
            $course = new Course();
            $course->setName("course $i");
            $course->setContent("Contenu du coure $i");
            $course->setDuration(mt_rand(1, 10));
            $course->setPublished(true);
            $course->setDateCreated(new \DateTimeImmutable());
            $manager->persist($course);
        }

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $course = new Course();
            $course->setName($faker->word);
            $course->setContent($faker->realText());
            $course->setDuration($faker->numberBetween(1, 10));
            $course->setPublished($faker->boolean);
            $date = $faker->dateTimeBetween('-30 days', '-1 days');
            $course->setDateCreated(\DateTimeImmutable::createFromMutable( $date));
            $manager->persist($course);
        }

        $manager->flush();

        // USER PAR DEFAUT
        $user = new User();
        $user->setUsername("user");

        // générer un mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, "123456");

        // setter le mot de passe généré
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();


    }
}
