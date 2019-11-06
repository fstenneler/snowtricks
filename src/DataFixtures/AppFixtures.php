<?php

namespace App\DataFixtures;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Trick;
use App\Services\Slug;
use App\Entity\Comment;
use App\Entity\Category;
use Symfony\Component\Yaml\Yaml;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager, 'src/DataFixtures/Data/user.yaml');
        $this->loadCategories($manager, 'src/DataFixtures/Data/category.yaml');
        $this->loadTricks($manager, 'src/DataFixtures/Data/trick.yaml');
        $this->loadMedias($manager, 'src/DataFixtures/Data/media.yaml');
        $this->loadComments($manager, 'src/DataFixtures/Data/comment.yaml');
    }

    /**
     * Load users from Yaml data file and store into database
     *
     * @param ObjectManager $manager
     * @param string $dataPath
     * @return void
     */
    private function loadUsers(ObjectManager $manager, $dataPath)
    {
        $fixtureData = Yaml::parseFile($dataPath);

        foreach($fixtureData as $i => $userData) {
            $user = new User();
            $user->setUserName($userData['userName']);
            $user->setEmail($userData['email']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $userData['password']));            
            $user->setAvatar($userData['avatar']);
            $user->setActivated($userData['activated']);
            $manager->persist($user);
            $this->addReference('[user] ' . $i, $user);
        }

        $manager->flush();
    }

    /**
     * Load categories from Yaml data file and store into database
     *
     * @param ObjectManager $manager
     * @param string $dataPath
     * @return void
     */
    private function loadCategories(ObjectManager $manager, $dataPath)
    {
        $fixtureData = Yaml::parseFile($dataPath);

        foreach($fixtureData as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $category->setSlug(Slug::createSlug($categoryName));
            $manager->persist($category);
            $this->addReference('[category] ' . $categoryName, $category);
        }

        $manager->flush();
    }

    /**
     * Load tricks from Yaml data file and store into database
     *
     * @param ObjectManager $manager
     * @param string $dataPath
     * @return void
     */
    private function loadTricks(ObjectManager $manager, $dataPath)
    {
        $fixtureData = Yaml::parseFile($dataPath);

        foreach($fixtureData as $trickData) {
            $trick = new Trick();
            $trick->setCategory($this->getReference('[category] ' . $trickData['categoryName']));
            $trick->setUser($this->getReference('[user] 1'));
            $trick->setName($trickData['name']);
            $trick->setDescription($trickData['description']);
            $trick->setCreationDate(new \DateTime($trickData['creationDate']));
            $trick->setSlug(Slug::createSlug($trickData['name']));
            $manager->persist($trick);
            $this->addReference('[trick] ' . $trickData['name'], $trick);
        }

        $manager->flush();
    }

    /**
     * Load medias from Yaml data file and store into database
     *
     * @param ObjectManager $manager
     * @param string $dataPath
     * @return void
     */
    private function loadMedias(ObjectManager $manager, $dataPath)
    {
        $fixtureData = Yaml::parseFile($dataPath);

        foreach($fixtureData as $trickData) {
            foreach($trickData['media'] as $url) {
                $media = new Media();
                $media->setTrick($this->getReference('[trick] ' . $trickData['trickName']));
                $media->setUrl($url);
                $manager->persist($media);
            }
        }

        $manager->flush();
    }

    /**
     * Load comments from Yaml data file and store into database
     *
     * @param ObjectManager $manager
     * @param string $dataPath
     * @return void
     */
    private function loadComments(ObjectManager $manager, $dataPath)
    {
        $fixtureData = Yaml::parseFile($dataPath);

        foreach($fixtureData as $message) {
            
            $comment = new Comment();
            $comment->setTrick($this->getReference('[trick] Melon grab'));
            $comment->setUser($this->getReference('[user] ' . rand(0,9)));
            $comment->setMessage($message);

            $randomTimestamp = rand(mktime(0, 0, 0, 10, 1, 2019), time());
            $comment->setCreationDate(new \DateTime(date("Y-m-d H:i:s", $randomTimestamp)));
            
            $manager->persist($comment);
        }

        $manager->flush();
    }

}
