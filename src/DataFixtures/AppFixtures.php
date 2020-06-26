<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder ;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('FR-fr');

        $adminUser = new User();
        $adminUser->setFirstName('Lassana')
                  ->setLastName('Diakité')
                  ->setEmail('ld@gmail.com')
                  ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                  ->setPicture('https://netrinoimages.s3.eu-west-2.amazonaws.com/2012/05/26/136161/57586/ac_cobra_shelby_cobra_3d_model_c4d_max_obj_fbx_ma_lwo_3ds_3dm_stl_506117_o.jpg')
                  ->setIntroduction($faker->sentence($nbWords = 15))
                  ->setDescription('<p>'. join('</p><p>', $faker->paragraphs(3)) . '</p>')
                  ->setRoles(['ROLE_ADMIN']);

        $manager->persist($adminUser);
        
        
        //Nous gérons les utilisateurs
        $users =[];
        $genres = ['male', 'female'];

        for($i = 1; $i <=10; $i++){
            $user = new User();

            
            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname)
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence($nbWords = 15, $variableNbWords = false))
                 ->setDescription('<p>'. join('</p><p>', $faker->paragraphs(3)) . '</p>')
                 ->setHash($hash)
                 ->setPicture($picture);

            $manager->persist($user);
            $users []= $user;
        }


        // les prestataires
            $usersPresta =[];
        for($j = 1; $j <=10; $j++){
            $userPresta = new User();

            
            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($userPresta, 'password');

            $userPresta->setFirstName($faker->firstname)
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence($nbWords = 15, $variableNbWords = false))
                 ->setDescription('<p>'. join('</p><p>', $faker->paragraphs(3)) . '</p>')
                 ->setHash($hash)
                 ->setPicture($picture)
                 ->setRoles(['ROLE_PRESTA']);

            $manager->persist($userPresta);
            $usersPresta[]= $userPresta;
        }

        //Nous gérons les catégories
        $categories = [];
        for($i=1; $i<=10; $i++){
            $category = new Category();
            $category->setTitle($faker->jobTitle)
                     ->setDescription('<p>'. join('</p><p>', $faker->paragraphs(2)) . '</p>');

            $manager->persist($category);
            $categories[]= $category;
        }

        //Nous gérons les annonces
        for($i=1; $i <= 30; $i++){
        $ad = new Ad(); 
        

        $title        = $faker->jobTitle;
        $coverImage   = $faker->imageUrl(1000,350);
        $introduction = $faker->sentence($nbWords = 15, $variableNbWords = false);
        $content      = '<p>'. join('</p><p>', $faker->paragraphs(4)) . '</p>';
       /* $updatedAt    = $faker->dateTime('now');
        ->setUpdatedAt($updatedAt)*/
        
        $userPresta     = $usersPresta[mt_rand(0, count($usersPresta) -1)];
        $category = $categories[mt_rand(0, count($categories) -1)];

        $ad->setTitle($title)
           ->setCoverImage($coverImage)
           ->setIntroduction($introduction)
           ->setContent($content)
           ->setPrice(mt_rand(40, 200))
           ->setAuthor($userPresta)
           ->setCategory($category);
        
        for($j = 1; $j <= mt_rand(2, 5); $j++){
            $image = new Image();

            $image->setUrl($faker->imageUrl())
                  ->setCaption($faker->sentence())
                  ->setAd($ad);

            $manager->persist($image);
        }

        // Gestion des réservations
        for ($j = 1; $j <= mt_rand(0, 10); $j++) {
            $booking = new Booking();

            $createdAt = $faker->dateTimeBetween('-6 months');
            $startDate = $faker->dateTimeBetween('-3 months');
            // Gestion de la date de fin
            $duration  = mt_rand(3, 10);
            $endDate   = (clone $startDate)->modify("+$duration days");

            $amount    =  $ad->getPrice() * $duration;
            $booker    = $users[mt_rand(0, count($users) - 1)];
            $comment   = $faker->paragraph();

            $booking->setBooker($booker)
                ->setAd($ad)
                ->setStartDate($startDate)
                ->setEndDate($endDate)
                ->setCreatedAt($createdAt)
                ->setAmount($amount)
                ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                ->setComment($comment)
               ;

            $manager->persist($booking);

            // Gestion des commentaires
            if (mt_rand(0, 1)) {
                $comment = new Comment();
                $comment->setContent($faker->paragraph())
                    ->setRating(mt_rand(1, 5))
                    ->setAuthor($booker)
                    ->setAd($ad);

                $manager->persist($comment);
            }
        }
        $manager->persist($ad);
        
        }
        $manager->flush();
    }
}
