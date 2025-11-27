<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Genre;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // Genres data
        $electronica = new Genre();
        $electronica->setName('Electronica');
        $manager->persist($electronica);
        $folk = new Genre();
        $folk->setName('Folk');
        $manager->persist($folk);
        $pop = new Genre();
        $pop->setName('Pop');
        $manager->persist($pop);

        $popRock = new Genre();
        $popRock->setName('Pop rock');
        $manager->persist($popRock);
        $indiePop = new Genre();
        $indiePop->setName('Indie pop');
        $manager->persist($indiePop);

        // Artists data
        $sia = new Artist();
        $sia->setName('Sia');
        $manager->persist($sia);
        $imagineDragons = new Artist();
        $imagineDragons->setName('Imagine Dragons');
        $manager->persist($imagineDragons);

        // User data
        $user = new User();
        $user->setEmail('test@gmail.com');
        $user->setUsername('test');
        // Hashing the user's password
        $password = $this->hasher->hashPassword($user, 'test123');
        $user->setPassword($password);
        $manager->persist($user);

        // Three users and an album with 60 reviews
        $faker = Factory::create();
        $users = [];
        for ($i = 1; $i <= 3; $i++) {
            $user = new User();
            $user->setEmail("test$i@example.com");
            $user->setUsername("TestUser$i");
            $user->setPassword($this->hasher->hashPassword($user, 'password123'));
            $manager->persist($user);
            $users[] = $user;
        }

        $genres = [];
        foreach (['Rock', 'Pop', 'Jazz', 'Electronic'] as $name) {
            $genre = new Genre();
            $genre->setName($name);
            $manager->persist($genre);
            $genres[] = $genre;
        }

        $artist = new Artist();
        $artist->setName('The Test Band');
        $artist->addGenre($genres[0]);
        $manager->persist($artist);

        $album = new Album();
        $album->setTitle('The Greatest Hits');
        $album->setArtist($artist);
        $album->setCreator($users[0]); // Created by User 1
        $album->setTrackList(['Track 1', 'Track 2', 'Track 3']);
        $album->addGenre($genres[0]);
        $manager->persist($album);
        for ($i = 0; $i < 60; $i++) {
            $review = new Review();
            $review->setRating($faker->numberBetween(1, 5));
            $review->setComment($faker->paragraph()); // Random text
            
            // Link to the one album
            $review->setAlbum($album);
            
            // Link to a random user from our list
            $randomUser = $faker->randomElement($users);
            $review->setReviewer($randomUser);

            $manager->persist($review);
        }


        $manager->flush();
    }
}
