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
        
        $rock = new Genre(); // Needed for later usage
        $rock->setName('Rock');
        $manager->persist($rock);
        
        $jazz = new Genre(); // Needed for later usage
        $jazz->setName('Jazz');
        $manager->persist($jazz);

        // Array of genres for random assignment later
        $allGenres = [$electronica, $folk, $pop, $popRock, $indiePop, $rock, $jazz];

        // Artists data
        $sia = new Artist();
        $sia->setName('Sia');
        $manager->persist($sia);
        $imagineDragons = new Artist();
        $imagineDragons->setName('Imagine Dragons');
        $manager->persist($imagineDragons);
        
        $testBand = new Artist();
        $testBand->setName('The Test Band');
        $testBand->addGenre($rock);
        $manager->persist($testBand);
        
        // Additional Artists for variety
        $coldplay = new Artist();
        $coldplay->setName('Coldplay');
        $manager->persist($coldplay);
        
        $daftPunk = new Artist();
        $daftPunk->setName('Daft Punk');
        $manager->persist($daftPunk);
        
        $adele = new Artist();
        $adele->setName('Adele');
        $manager->persist($adele);

        // User data
        $user = new User();
        $user->setEmail('test@gmail.com');
        $user->setUsername('test');
        $password = $this->hasher->hashPassword($user, 'test123');
        $user->setPassword($password);
        $manager->persist($user);

        // Three users
        $faker = Factory::create();
        $users = [$user]; // Include the main test user
        for ($i = 1; $i <= 3; $i++) {
            $newUser = new User();
            $newUser->setEmail("test$i@example.com");
            $newUser->setUsername("TestUser$i");
            $newUser->setPassword($this->hasher->hashPassword($newUser, 'password123'));
            $manager->persist($newUser);
            $users[] = $newUser;
        }

        // Original Album 1
        $album1 = new Album();
        $album1->setTitle('The Greatest Hits');
        $album1->setArtist($testBand);
        $album1->setCreator($users[1]); 
        $album1->setTrackList(['Track 1', 'Track 2', 'Track 3']);
        $album1->addGenre($rock);
        $manager->persist($album1);
        
        // Generate reviews for Album 1
        for ($i = 0; $i < 60; $i++) {
            $review = new Review();
            $review->setRating($faker->numberBetween(1, 5));
            $review->setComment($faker->paragraph());
            $review->setAlbum($album1);
            $review->setReviewer($faker->randomElement($users));
            $manager->persist($review);
        }

        // --- 6 New Albums ---

        // Album 2
        $album2 = new Album();
        $album2->setTitle('Night Visions');
        $album2->setArtist($imagineDragons);
        $album2->setCreator($users[0]);
        $album2->setTrackList(['Radioactive', 'Demons', 'On Top of the World']);
        $album2->addGenre($popRock);
        $album2->addGenre($indiePop);
        $manager->persist($album2);
        
        for ($i = 0; $i < 15; $i++) { // Fewer reviews for variety
            $review = new Review();
            $review->setRating($faker->numberBetween(3, 5));
            $review->setComment($faker->paragraph());
            $review->setAlbum($album2);
            $review->setReviewer($faker->randomElement($users));
            $manager->persist($review);
        }

        // Album 3
        $album3 = new Album();
        $album3->setTitle('1000 Forms of Fear');
        $album3->setArtist($sia);
        $album3->setCreator($users[2]);
        $album3->setTrackList(['Chandelier', 'Elastic Heart', 'Big Girls Cry']);
        $album3->addGenre($pop);
        $album3->addGenre($electronica);
        $manager->persist($album3);
        
        for ($i = 0; $i < 20; $i++) {
            $review = new Review();
            $review->setRating($faker->numberBetween(4, 5));
            $review->setComment($faker->paragraph());
            $review->setAlbum($album3);
            $review->setReviewer($faker->randomElement($users));
            $manager->persist($review);
        }

        // Album 4
        $album4 = new Album();
        $album4->setTitle('Random Access Memories');
        $album4->setArtist($daftPunk);
        $album4->setCreator($users[1]);
        $album4->setTrackList(['Give Life Back to Music', 'Get Lucky', 'Instant Crush']);
        $album4->addGenre($electronica);
        $album4->addGenre($pop);
        $manager->persist($album4);
        
        for ($i = 0; $i < 30; $i++) {
            $review = new Review();
            $review->setRating($faker->numberBetween(2, 5));
            $review->setComment($faker->paragraph());
            $review->setAlbum($album4);
            $review->setReviewer($faker->randomElement($users));
            $manager->persist($review);
        }

        // Album 5
        $album5 = new Album();
        $album5->setTitle('25');
        $album5->setArtist($adele);
        $album5->setCreator($users[3] ?? $users[0]);
        $album5->setTrackList(['Hello', 'Send My Love', 'I Miss You']);
        $album5->addGenre($pop);
        $album5->addGenre($folk); // Close enough for soul
        $manager->persist($album5);
        
        for ($i = 0; $i < 45; $i++) {
            $review = new Review();
            $review->setRating($faker->numberBetween(1, 5));
            $review->setComment($faker->paragraph());
            $review->setAlbum($album5);
            $review->setReviewer($faker->randomElement($users));
            $manager->persist($review);
        }

        // Album 6
        $album6 = new Album();
        $album6->setTitle('Parachutes');
        $album6->setArtist($coldplay);
        $album6->setCreator($users[0]);
        $album6->setTrackList(['Don\'t Panic', 'Shiver', 'Yellow']);
        $album6->addGenre($popRock);
        $manager->persist($album6);
        
        for ($i = 0; $i < 10; $i++) {
            $review = new Review();
            $review->setRating($faker->numberBetween(3, 4));
            $review->setComment($faker->paragraph());
            $review->setAlbum($album6);
            $review->setReviewer($faker->randomElement($users));
            $manager->persist($review);
        }

        // Album 7 (A newer one with few reviews)
        $album7 = new Album();
        $album7->setTitle('Mercury – Act 1');
        $album7->setArtist($imagineDragons);
        $album7->setCreator($users[2]);
        $album7->setTrackList(['My Life', 'Lonely', 'Wrecked']);
        $album7->addGenre($popRock);
        $manager->persist($album7);
        
        for ($i = 0; $i < 5; $i++) {
            $review = new Review();
            $review->setRating($faker->numberBetween(2, 5));
            $review->setComment($faker->paragraph());
            $review->setAlbum($album7);
            $review->setReviewer($faker->randomElement($users));
            $manager->persist($review);
        }

        $manager->flush();
    }
}