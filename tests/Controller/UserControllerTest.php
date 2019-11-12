<?php

namespace App\Tests\Controller;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Controller\UserController;
use App\Repository\UserRepository;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class UserControllerTest extends WebTestCase
{

    protected static $application;
    protected $entityManager;

    /**
     * Set up the test
     * Purge database and load fixtures
     *
     * @return void
     */
    protected function setUp()
    {
        //self::runCommand('doctrine:database:drop --force --env=test');
        //self::runCommand('doctrine:database:create --env=test');
        //self::runCommand('doctrine:schema:update --force --env=test');
        //self::runCommand('doctrine:fixtures:load --env=test --no-interaction');

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Run console command
     *
     * @param string $command
     * @return self
     */
    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);
        return self::getApplication()->run(new StringInput($command));
    }

    /**
     * get self application
     *
     * @return self
     */
    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();
            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }
        return self::$application;
    }

    /**
     * check if the register route
     *
     * @return void
     */
    public function testRegister()
    {
        
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertGreaterThan(
            0,
            $crawler->filter('html h1:contains("Registration")')->count()
        );
        
    }

    /**
     * Test a new user registration
     *
     * @return void
     */
    /*public function testRegisterNewUser()
    {
        
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Create an account')->form();
        $form['registration[userName]'] = 'user1';
        $form['registration[email]'] = 'test@orlinstreet.rocks';
        $form['registration[password]'] = 'azerty';
        $crawler = $client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filterXPath('//span[@class="form-error-message"]')->count()
        );
        
    }*/

    public function testRegisterExistingUser()
    {
        
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Create an account')->form();
        $form['registration[userName]'] = 'Jimmy';
        $form['registration[email]'] = 'jimmy@orlinstreet.rocks';
        $form['registration[password]'] = 'azerty';
        $crawler = $client->submit($form);

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findBy(['userName' => 'Jimmy']);

        $this->assertSame(1, count($user));

        $this->assertGreaterThan(
            0,
            $crawler->filterXPath('//span[@class="form-error-message"]')->count()
        );
        
    }
    
}