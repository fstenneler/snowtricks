<?php

namespace App\DataFixtures;

use App\Services\Slug;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Category;
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

    public function load(ObjectManager $manager)
    {

        //create 10 users
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUserName('user' . $i);
            $user->setEmail('user' . $i . '@orlinstreet.rocks');
            $user->setPassword($this->passwordEncoder->encodePassword($user,'azerty'));
            $user->setAvatar('person_' . rand(1,4) . '.jpg');
            $user->setActivated(true);
            $manager->persist($user);
            $this->addReference('user-' . $i, $user);
        }

        // Create 5 categories
        for ($i = 1; $i <= 5; $i++) {
            $category = new Category();
            $category->setName('Category ' . $i);            
            $category->setSlug(Slug::createSlug('Category '.$i));            
            $manager->persist($category);
            $this->addReference('category-' . $i, $category);
        }


        // Create 50 tricks
        $mediaStart = 0;
        for ($iTrick = 1; $iTrick <= 50; $iTrick++) {

            // Trick
            $trick = new Trick();
            $trick->setName('Trick '.$iTrick);
            $description = "Trick " . $iTrick . "\n\n";
            $description .= "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin placerat tincidunt molestie. Nam in neque nulla. Duis augue massa, blandit eget sapien sed, bibendum efficitur neque. Vestibulum ut pellentesque orci, vel feugiat nulla. Fusce elementum diam nec neque tincidunt, ac euismod mauris fermentum. Etiam at nunc sagittis, varius felis a, luctus felis. Ut ac auctor ligula. Maecenas vitae urna lacinia, posuere purus in, pulvinar mauris. Aliquam id sapien imperdiet, dignissim odio sit amet, tincidunt est. Maecenas mattis, nunc quis tincidunt congue, sapien nunc gravida velit, vitae placerat sapien leo nec tellus. Donec vel tempus ligula. Morbi condimentum ultricies nulla, at pulvinar erat tincidunt a. Nulla massa justo, bibendum vel rhoncus sed, feugiat mollis metus. Pellentesque imperdiet molestie lacus, a tincidunt nunc posuere vitae. Quisque vitae dapibus tellus.\n\n";
            $description .= "Proin ac risus a enim ultrices aliquam. Curabitur auctor lectus ex, id tincidunt metus varius ac. Morbi felis tellus, iaculis in odio eu, accumsan tincidunt nunc. Cras maximus ante ex, vel vehicula massa ultricies sit amet. Sed in convallis dui. Pellentesque nec libero id nisl lobortis luctus. Mauris aliquet turpis commodo odio blandit, in vulputate magna aliquam. Maecenas vitae ultricies enim. Etiam augue felis, suscipit nec urna sed, imperdiet gravida nisi.\n\n";                 
            $description .= "Sed et lorem rhoncus massa eleifend aliquet. Nulla pulvinar arcu quis congue molestie. Fusce eget fringilla tortor, nec iaculis diam. Mauris nec orci eu nisl facilisis lacinia. Sed vestibulum velit ac dui lacinia, et sagittis enim aliquet. Ut vel mi id nibh maximus dignissim ut in sapien. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.\n\n";                        
            $description .= "Nulla non faucibus tortor, in pharetra dolor. Ut lobortis turpis vitae lorem fringilla, at rhoncus nisl congue. Fusce tincidunt consectetur tempor. Quisque a sem non lacus pharetra semper at elementum leo. Integer et eros nisi. Vivamus ut euismod mauris. Praesent maximus justo est, id vestibulum enim hendrerit sed. In mauris velit, porta quis tincidunt id, suscipit id sapien. Vivamus quis magna sit amet sapien rutrum feugiat sed ac neque. Sed rutrum quam vehicula, sagittis lectus vel, sodales justo. Aenean non augue vitae nisl molestie congue a eget lectus. Donec condimentum, dolor a euismod congue, turpis lectus tristique enim, ullamcorper cursus velit metus ut massa. Maecenas ornare nec leo vel dapibus.\n\n";                        
            $description .= "Mauris dapibus sollicitudin iaculis. Phasellus eu ornare nisi. Aliquam posuere ac metus et hendrerit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec felis justo, hendrerit sit amet nunc eget, dictum consequat purus. Sed id ante a tellus pretium aliquam. Donec quam massa, dictum quis erat ac, euismod eleifend nulla. Nulla ante tellus, blandit eu suscipit ac, ornare eu lorem. Maecenas maximus eros at justo porttitor consectetur. Nunc vehicula orci egestas, interdum augue id, aliquet ex. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Donec cursus libero eget massa venenatis, congue lacinia odio faucibus. Fusce placerat, risus a iaculis commodo, mauris enim venenatis risus, non eleifend massa erat in ipsum.\n\n";
            $trick->setDescription($description);
            $trick->setCreationDate(
                new \DateTime(
                    date(
                        'Y-m-d\TH:i:s.u', 
                        mt_rand(
                            mktime(0,0,0,1,1,2019),
                            time()
                        )
                    )
                )
            );
            $trick->setCategory($this->getReference('category-' . rand(1,5)));
            $trick->setSlug(Slug::createSlug('Trick '.$iTrick));
            $trick->setUser($this->getReference('user-' . rand(1,10)));
            $manager->persist($trick);
            $this->addReference('trick-' . $iTrick, $trick);

            //Media

            // Create 3 pictures on Media
            $headerRand = rand(1,3);
            for ($i = 1; $i <= 3; $i++) {
                $media = new Media();
                $media->setUrl('trick_media_' . rand(1,6) . '.jpg');
                $media->setTrick($this->getReference('trick-' . $iTrick));
                $manager->persist($media);
                if($mediaStart == 6) {
                    $mediaStart = 0;
                }
            }

            // Create 3 videos on Media
            for ($i = 1; $i <= 3; $i++) {
                $media = new Media();
                $media->setUrl('https://www.youtube.com/embed/hPuVJkw1MmI');
                $media->setTrick($this->getReference('trick-' . $iTrick));
                $manager->persist($media);
            }


            //Comment

            // Create 7 comments
            for ($i = 1; $i <= 3; $i++) {
                $comment = new Comment();
                $comment->setMessage("Comment " . $i . " - Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur quidem laborum necessitatibus, ipsam impedit vitae autem, eum officia, fugiat saepe enim sapiente iste iure! Quam voluptas earum impedit necessitatibus, nihil?");
                $comment->setCreationDate(
                    new \DateTime(
                        date(
                            'Y-m-d\TH:i:s.u', 
                            mt_rand(
                                mktime(0,0,0,1,1,2019),
                                time()
                            )
                        )
                    )
                );
                $comment->setTrick($this->getReference('trick-' . $iTrick));
                $comment->setUser($this->getReference('user-' . rand(1,10)));
                $manager->persist($comment);
            }

        }

        $manager->flush();
    }

}