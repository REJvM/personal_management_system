<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Entity\BlogPost;
use App\Entity\FileUpload;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const DUMMY_CONTENT = '<h1>Congratulations on setting up the Project</h1><p>This is an example post.</p><h3>What\'s next?</h3><ol><li><strong>Click through the website:</strong> Take a look around all the content on the website.</li><li><strong>Create new content:</strong> Experiment with different features and create new blog post or other content.</li><li><strong>Send a message:</strong> If you like the website, send me a message</li></ol>';
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ){  
    }

    public function load(ObjectManager $manager): void
    {
        $adminUser = new User();
        $adminUser->setEmail('admin@test.com');
        $adminUser->setPassword(
            $this->userPasswordHasher->hashPassword(
                $adminUser, 
                'admin'
            )
        );
        $adminUser->setRoles(["ROLE_ADMIN"]);
        $manager->persist($adminUser);

        $firstBlogPost = new BlogPost();
        $firstBlogPost->setTitle('My first Blog post');
        $firstBlogPost->setContent(self::DUMMY_CONTENT);
        $firstBlogPost->setCategory(BlogPost::CATEGORY_PROJECTS);
        $firstBlogPost->setCreatedBy($adminUser);
        $firstBlogPost->setCreatedOn(new DateTime('yesterday'));
        $firstBlogPost->setModifiedBy($adminUser);
        $firstBlogPost->setModifiedOn(new DateTime());
        $manager->persist($firstBlogPost);

        
        $secondBlogPost = new BlogPost();
        $secondBlogPost->setTitle('My Second Blog post');
        $secondBlogPost->setContent(self::DUMMY_CONTENT);
        $secondBlogPost->setCategory(BlogPost::CATEGORY_ARCHIVES);
        $secondBlogPost->setCreatedBy($adminUser);
        $secondBlogPost->setCreatedOn(new DateTime());
        $manager->persist($secondBlogPost);

        
        $firstBackgroundImage = new FileUpload();
        $firstBackgroundImage->setName('chefchaouen-123abc.jpg');
        $firstBackgroundImage->setCreatedBy($adminUser);
        $firstBackgroundImage->setCreatedOn(new DateTime());
        $manager->persist($firstBackgroundImage);

        $secondBackgroundImage = new FileUpload();
        $secondBackgroundImage->setName('chiangmai-abc123.jpg');
        $secondBackgroundImage->setCreatedBy($adminUser);
        $secondBackgroundImage->setCreatedOn(new DateTime());
        $manager->persist($secondBackgroundImage);

        $thirdbackgroundImage = new FileUpload();
        $thirdbackgroundImage->setName('china-456def.jpg');
        $thirdbackgroundImage->setCreatedBy($adminUser);
        $thirdbackgroundImage->setCreatedOn(new DateTime());
        $manager->persist($thirdbackgroundImage);

        
        $manager->flush();
    }
}
