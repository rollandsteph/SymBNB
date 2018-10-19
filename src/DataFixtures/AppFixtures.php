<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Annonce;
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
            $users[]=$user;
        }
        // gestion des annonces
        for ($i = 1; $i <= 30; $i++) {
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
            $manager->persist($annonce);
        }
        $manager->flush();
    }
}
