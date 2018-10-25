<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Annonce;
use App\Entity\Booking;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder=$encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create("fr_FR");

        $users=[];
        $genres=['male','female'];

        $adminRole=new Role();
        $adminRole->setTitle('ROLE_ADMIN');

        $adminUser=new user();
        $adminUser->setFirstName('Rolland')
                    ->setLastName('Steph')
                    ->setEmail('rolland.Steph@gmail.com')
                    ->setHash($this->encoder->encodePassword($adminUser,'test'))
                    ->setPicture('https://randomuser.me/api/portraits/men/21.jpg')
                    ->setIntroduction($faker->sentence())
                    ->setDescription("<p>" . join("<p></p>", $faker->paragraphs(3)) . "</p>")
                    ->addUserRole($adminRole);
                    $manager->persist($adminUser);



        $manager->persist($adminRole);

        // gestion des utilisateurs
        for ($i = 1; $i <= 10; $i++) {

            $user = new User();
            $genre=$faker->randomElement($genres);
            $url="https://randomuser.me/api/portraits/";
            $image=mt_rand(1,99).".jpg";

            if($genre=='male'){
                $url .="men/".$image;
            }else{
                $url .="women/".$image;
            }

            $hash=$this->encoder->encodePassword($user,'password');

            $user	->setFirstName($faker->firstname($genre))
                    ->setLastName($faker->lastname)
                    ->setEmail($faker->email)
                    ->setIntroduction($faker->sentence())
                    ->setDescription("<p>" . join("<p></p>", $faker->paragraphs(3)) . "</p>")
                    ->setPicture($url)
                    ->setHash($hash);

            $manager->persist($user);
            $users[]=$user; // on ajoute le user créé dans un tableau de user
        }
        // gestion des annonces
        for ($i = 1; $i <= 50; $i++) {
            $annonce = new Annonce();
            $annonce	->setTitle($faker->sentence(5))
                        ->setCoverImage($faker->imageUrl(1000, 350))
                        ->setIntroduction($faker->paragraph(2))
                        ->setContent("<p>" . join("<p></p>", $faker->paragraphs(5)) . "</p>")
                        ->setPrice(mt_rand(40, 200))
                        ->setRooms(mt_rand(1, 5))
                        ->setAuthor($users[mt_rand(0,count($users)-1)]); // on attribut un user au hasard dans le tableau des users

            //gestion des images des annonces            
            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();
                $image	->setAnnonce($annonce)
                        ->setCaption($faker->sentence())
                        ->setUrl($faker->imageUrl());
                $manager->persist($image);

            }
            // gestion des réservations
            for($j = 1; $j<= mt_rand(0,10); $j++ ){
                $duration=mt_rand(3,10); // durée de la réservation

                $booking=new Booking();
                $booking	->setCreatedAt($faker->dateTimeBetween('-6 months')) // trouve une date d'annonce entre aujourd'hui et il y a 6 mois
                            ->setStartDate($faker->dateTimeBetween('-3 months'))
                //on prend la startDate que l'on clone (pour qu'elle ne soit pas modifiée) et à laquelle on ajoute la duration
                // pour trouver la date de fin
                            ->setEndDate((clone $booking->getStartDate())->modify("+$duration days"))
                            ->setAmount($annonce->getPrice()*$duration) // on calcul le montant
                            ->setBooker($users[mt_rand(0, count($users)-1)]) // on affecte un user au hasard
                            ->setAnnonce($annonce);

                $manager->persist($booking);
            }
            $manager->persist($annonce);
        }
        $manager->flush();
    }
}
