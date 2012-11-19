<?php

namespace IBW\WebsiteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use IBW\WebsiteBundle\Entity\User;
use IBW\WebsiteBundle\Entity\StairsActivity;
use IBW\WebsiteBundle\Tests\DatabaseInit;

/**
 * Functional tests for StairsController.php
 */
class StairsControllerTest extends WebTestCase
{

    /**
     * Test Sf firewall
     * Done?
     *  [#] Test correct page display
     *  [#] Test correct user register
     *  [#] Test existing user register
     *  [#] Test existing user register but wrong password
     *  [#] Test user register email field not email type
     *  [#] Test user register email field empty
     *  [#] Test user register password field empty
     * 
     * @return void
     */
    public function testSfFirewall()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        DatabaseInit::databasePopulation($kernel);
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Symfony Firewall' . "\n";
        echo '---------------------------' . "\n";

        echo '[#] Test if unauthenticated user has access to Home page';

        $client = static::createClient();
        $crawler = $client->request('GET', '/stairs');
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::indexAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        echo ' . . . Ok' . "\n";

        echo '[#] Test if unauthenticated user has access to Register page';

        $crawler = $client->request('GET', '/stairs/register');
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::registerAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        echo ' . . . Ok' . "\n";

        echo '[#] Test if unauthenticated user has access to login page';

        $crawler = $client->request('GET', '/stairs/login');
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        echo ' . . . Ok' . "\n";

        echo '[#] Test if unauthenticated user has no access to Activities page';

        $crawler = $client->request('GET', '/stairs/activities');
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction', 
                    $client->getRequest()->attributes->get('_controller'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test if unauthenticated user has no access to Activity page';

        $crawler = $client->request('GET', '/stairs/activities/123');
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction', 
                    $client->getRequest()->attributes->get('_controller'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test if unauthenticated user has no access to Stats page';

        $crawler = $client->request('GET', '/stairs/stats');
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction',
                    $client->getRequest()->attributes->get('_controller'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test if unauthenticated user has no access to Teams page';

        $crawler = $client->request('GET', '/stairs/team');
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction',
                    $client->getRequest()->attributes->get('_controller'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test if unauthenticated user has no access to Team page';

        $crawler = $client->request('GET', '/stairs/team/321');
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction', 
                    $client->getRequest()->attributes->get('_controller'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test if unauthenticated user has no access to Top page';

        $crawler = $client->request('GET', '/stairs/top');
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction', 
                    $client->getRequest()->attributes->get('_controller'));
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test registerIndexAction() function from Stairs Controller
     * Done?
     *  [#] Test correct page display
     *  [#] Test correct user register
     *  [#] Test if user is registered in database
     *  [#] Test existing user register
     *  [#] Test existing user register but wrong password
     *  [#] Test user register email field not email type
     *  [#] Test user register email field empty
     *  [#] Test user register password field empty
     * 
     * @return void
     */
    public function testRegisterIndex()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Regstration Page' . "\n";
        echo '---------------------------' . "\n";

        echo '[#] Test correct page display';
        $client = static::createClient();
        $crawler = $client->request('GET', '/stairs/register');
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::registerAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'text/html; charset=UTF-8'));
        $this->assertCount(2, $crawler->filter('form'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test correct user register';

        $buttonCrawlerNode = $crawler->selectButton('Register');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_usertype[email]' => 'test@test.com',
            'ibw_websitebundle_usertype[password]' => 'password',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::registerAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('User Created!', $crawler->filter('div.alert')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test if user is registered in database';
        $user = new User();
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('IBWWebsiteBundle:User')->findOneByEmail('test@test.com');
        $this->assertNotNull($user);
        echo ' . . . Ok' . "\n";

        echo '[#] Test existing user register';

        $crawler = $client->request('GET', '/stairs/register');
        $buttonCrawlerNode = $crawler->selectButton('Register');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_usertype[email]' => 'test@test.com',
            'ibw_websitebundle_usertype[password]' => 'password',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::registerAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertFalse($client->getResponse()->isRedirect());
        $this->assertEquals('Something went wrong', $crawler->filter('div.alert')->text());
        $this->assertEquals('Email already used', $crawler->filter('form > div > div > ul > li')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test existing user register but wrong password';

        $crawler = $client->request('GET', '/stairs/register');
        $buttonCrawlerNode = $crawler->selectButton('Register');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_usertype[email]' => 'test@test.com',
            'ibw_websitebundle_usertype[password]' => 'password1',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::registerAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('Something went wrong', $crawler->filter('div.alert')->text());
        $this->assertEquals('Email already used', $crawler->filter('form > div > div > ul > li')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test user register email field not email';

        $crawler = $client->request('GET', '/stairs/register');
        $buttonCrawlerNode = $crawler->selectButton('Register');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_usertype[email]' => 'test@',
            'ibw_websitebundle_usertype[password]' => 'password',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::registerAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('Something went wrong', $crawler->filter('div.alert')->text());
        $this->assertEquals('Not a valid email', $crawler->filter('form > div > div > ul > li')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test user register email field empty';

        $crawler = $client->request('GET', '/stairs/register');
        $buttonCrawlerNode = $crawler->selectButton('Register');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_usertype[email]' => '',
            'ibw_websitebundle_usertype[password]' => 'password',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::registerAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertGreaterThan(0, $crawler->filter('div.alert')->count());
        $this->assertEquals('Something went wrong', $crawler->filter('div.alert')->text());
        $this->assertEquals('Email required', $crawler->filter('form > div > div > ul > li')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test user register password field empty';

        $crawler = $client->request('GET', '/stairs/register');
        $buttonCrawlerNode = $crawler->selectButton('Register');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_usertype[email]' => 'test1@test.com',
            'ibw_websitebundle_usertype[password]' => '',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::registerAction', $client->getRequest()
                ->attributes->get('_controller'));
        $this->assertGreaterThan(0, $crawler->filter('div.alert')->count());
        $this->assertEquals('Something went wrong', $crawler->filter('div.alert')->text());
        $this->assertEquals('Password required', $crawler->filter('form > div > div > ul > li')->text());
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test loginIndexAction() function from Stairs Controller
     * Done?
     *  [#] Test correct page display
     *  [#] Test correct user register
     *  [#] Test existing user register
     *  [#] Test existing user register but wrong password
     *  [#] Test user register email field not email type
     *  [#] Test user register email field empty
     *  [#] Test user register password field empty
     * 
     * @return void
     */
    public function testLoginIndex()
    {

        echo "\n" . '---------------------------' . "\n";
        echo 'Test Login Page' . "\n";
        echo '---------------------------' . "\n";

        echo '[#] Test correct page display';
        $client = static::createClient();
        $crawler = $client->request('GET', '/stairs/login');
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($client->getResponse()->headers
                        ->contains('Content-Type', 'text/html; charset=UTF-8'));
        $this->assertCount(1, $crawler->filter('form'));

        echo ' . . . Ok' . "\n";

        echo '[#] Test login with wrong password';

        $buttonCrawlerNode = $crawler->selectButton('Login');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            '_username' => 'test@test.com',
            '_password' => 'password1',
                ));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('Bad credentials', $crawler->filter('div.alert')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test user register email field not email';

        $crawler = $client->request('GET', '/stairs/login');
        $buttonCrawlerNode = $crawler->selectButton('Login');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            '_username' => 'test@testcom',
            '_password' => 'password1',
                ));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('Bad credentials', $crawler->filter('div.alert')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test user register email field empty';

        $crawler = $client->request('GET', '/stairs/login');
        $buttonCrawlerNode = $crawler->selectButton('Login');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            '_username' => '',
            '_password' => 'password1',
                ));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('Bad credentials', $crawler->filter('div.alert')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test user register password field empty';

        $crawler = $client->request('GET', '/stairs/login');
        $buttonCrawlerNode = $crawler->selectButton('Login');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            '_username' => 'test@test.com',
            '_password' => '',
                ));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::loginAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('Bad credentials', $crawler->filter('div.alert')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test correct user login';

        $buttonCrawlerNode = $crawler->selectButton('Login');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            '_username' => 'test@test.com',
            '_password' => 'password',
                ));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        $this->assertEquals('test@test.com', 
                    $client->getContainer()->get('security.context')->getToken()->getUser()->getEmail());
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::indexAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals(1, $crawler->filter('ul.navbar-text:contains("Welcome test@test.com")')->count());
        $client->restart();
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test ActivitiesIndexAction() function from Stairs Controller
     * Done?
     *  [#] Test correct render of page
     *  [#] Test correct add of activity
     *  [#] Test add of activity with amount not number
     *  [#] Test add of activity with longitude an latitude not decimal
     *  [#] Test request of individual activity page with wrong id
     *  [#] Test render of individual activity page
     *  [#] Test change location
     *  [#] Test change amount
     *  [#] Test change amount to a non numeric value
     *  [#] Test change location to non decimal value
     *  [#] Test delete activity & showing deleted activities after delete
     *  [#] Test render of individual activity page when activity is deleted
     *  [#] Test delete of a deleted activity
     *  [#] Test edit a deleted activity
     *  [#] Test edit of a deleted activity
     * 
     * @return void
     */
    public function testActivitiesIndex()
    {

        echo "\n" . '---------------------------' . "\n";
        echo 'Test Activities Page' . "\n";
        echo '---------------------------' . "\n";

        echo '[#] Test correct render of page';

        $client = static::createClient(array(), array(
                    'PHP_AUTH_USER' => 'test@test.com',
                    'PHP_AUTH_PW' => 'password',
                ));
        $crawler = $client->request('GET', '/stairs/activities');
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::activitiesIndexAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'text/html; charset=UTF-8'));
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertCount(1, $crawler->filter('table'));
        $this->assertEquals('Add activity:', $crawler->filter('label')->text());
        $this->assertEquals(' Activities ', $crawler->filter('h4')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test correct add of activity';

        $buttonCrawlerNode = $crawler->selectButton('Add');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_stairsactivitytype[amount]' => '50',
                ));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('Add activity:', $crawler->filter('label')->text());
        $this->assertEquals(' Activities ', $crawler->filter('h4')->text());
        $this->assertEquals($client->getContainer()->getParameter('message_activity_created'), 
                    $crawler->filter('div.alert')->text());
        $this->assertEquals('50', $crawler->filter('table > tbody > tr > td')->eq(1)->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test correct add of activity';
        $stairActivity = new StairsActivity();
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $stairActivity = $em->getRepository('IBWWebsiteBundle:StairsActivity')->findOneByAmount(50);
        $this->assertNotNull($stairActivity);
        echo ' . . . Ok' . "\n";

        echo '[#] Test add of activity with amount not number';

        $buttonCrawlerNode = $crawler->selectButton('Add');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_stairsactivitytype[amount]' => 'amount',
                ));
        $this->assertFalse($client->getResponse()->isRedirect());
        $this->assertEquals('Add activity:', $crawler->filter('label')->text());
        $this->assertEquals(' Activities ', $crawler->filter('h4')->text());
        $this->assertEquals('This value should be a valid number.', 
                    $crawler->filter('form > div > div > ul > li')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test add of activity with longitude an latitude not decimal';

        $buttonCrawlerNode = $crawler->selectButton('Add');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_stairsactivitytype[amount]' => '22',
            'ibw_websitebundle_stairsactivitytype[lng]' => 'lng',
            'ibw_websitebundle_stairsactivitytype[lat]' => 'lng',
                ));
        $this->assertFalse($client->getResponse()->isRedirect());
        $this->assertEquals('Add activity:', $crawler->filter('label')->text());
        $this->assertEquals(' Activities ', $crawler->filter('h4')->text());
        $this->assertEquals('This value is not valid.', 
                    $crawler->filter('form > div')->eq(2)->filter('div')->eq(0)->filter('div > ul > li')->text());
        $this->assertEquals('This value is not valid.', 
                    $crawler->filter('form > div')->eq(2)->filter('div')->eq(1)->filter('div > ul > li')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test render of individual activity page with wrong id';

        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $activity = $em->getRepository('IBWWebsiteBundle:StairsActivity')->findOneByAmount(50);
        $crawler = $client->request('GET', '/stairs/activities/' . ($activity->getId() + 247938) . '');
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::activityAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isNotFound());
        echo ' . . . Ok' . "\n";

        echo '[#] Test render of individual activity page';

        $crawler = $client->request('GET', '/stairs/activities/' . $activity->getId() . '');
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::activityAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'text/html; charset=UTF-8'));
        $this->assertCount(2, $crawler->filter('form'));
        $this->assertCount(1, $crawler->filter('table'));
        $this->assertEquals($activity->getId(), $crawler->filter('table > tbody > tr > td')->eq(0)->text());
        $this->assertEquals($activity->getAmount(), $crawler->filter('table > tbody > tr > td')->eq(1)->text());
        $this->assertEquals('Amount', $crawler->filter('label')->text());
        $this->assertEquals('Longitude', $crawler->filter('label')->eq(2)->text());
        $this->assertEquals('Latitude', $crawler->filter('label')->eq(1)->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test edit activity';

        $buttonCrawlerNode = $crawler->selectButton('Update');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_stairsactivitytype[amount]' => '27',
            'ibw_websitebundle_stairsactivitytype[lng]' => '27.5898077',
            'ibw_websitebundle_stairsactivitytype[lat]' => '47.1549887',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::activityEditAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::activityAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('27', $crawler->filter('table > tbody > tr > td')->eq(1)->text());
        $this->assertEquals('Stair Activity updated!', $crawler->filter('div.alert')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test change amount to a non numeric value';
        $crawler = $client->request('GET', '/stairs/activities/' . $activity->getId() . '');
        $buttonCrawlerNode = $crawler->selectButton('Update');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_stairsactivitytype[amount]' => 'amount',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::activityEditAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertFalse($client->getResponse()->isRedirect());
        $this->assertEquals('This value should be a valid number.', 
                    $crawler->filter('form')->eq(1)->filter('div > div > ul > li')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test change location to non decimal value';
        $crawler = $client->request('GET', '/stairs/activities/' . $activity->getId() . '');
        $buttonCrawlerNode = $crawler->selectButton('Update');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_stairsactivitytype[lng]' => 'lng',
            'ibw_websitebundle_stairsactivitytype[lat]' => 'lat',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::activityEditAction',
                $client->getRequest()->attributes->get('_controller'));
        $this->assertFalse($client->getResponse()->isRedirect());
        $this->assertEquals('This value is not valid.', 
                    $crawler->filter('form')->eq(1)->children()->eq(2)->filter('div > ul > li')->text());
        $this->assertEquals('This value is not valid.', 
                    $crawler->filter('form')->eq(1)->children()->eq(3)->filter('div > ul > li')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test delete activity & display activities after delete';
        $crawler = $client->request('GET', '/stairs/activities/' . $activity->getId() . '');
        $buttonCrawlerNode = $crawler->selectButton('Delete');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form);
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::activityDelAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->headers
                        ->contains('Content-Type', 'text/html; charset=UTF-8'));
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertCount(1, $crawler->filter('table'));
        $this->assertEquals('Stair Activity marked for deletion!', $crawler->filter('div.alert')->text());
        $this->assertEquals('No activities. ', $crawler->filter('table > tbody > tr > td')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test render of individual activity page when activity is deleted';

        $crawler = $client->request('GET', '/stairs/activities/' . $activity->getId() . '');
        $this->assertTrue($client->getResponse()->isNotFound());
        echo ' . . . Ok' . "\n";

        echo '[#] Test delete of a deleted activity';
        $crawler = $client->request('GET', '/stairs/activities/delete/' . $activity->getId() . '');
        $this->assertTrue($client->getResponse()->isNotFound());
        echo ' . . . Ok' . "\n";

        echo '[#] Test edit a deleted activity';
        $crawler = $client->request('GET', '/stairs/activities/edit/' . $activity->getId() . '');
        $this->assertTrue($client->getResponse()->isNotFound());
        echo ' . . . Ok' . "\n";

        echo '[#] Test edit of a deleted activity';
        $crawler = $client->request('GET', '/stairs/activities/edit/' . $activity->getId() . '');
        $this->assertTrue($client->getResponse()->isNotFound());
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test statsIndexAction() function from Stairs Controller
     * Done?
     *  [#] Test correct render of page (user with no activities)
     *  [#] Test correct render of page (user with activities)
     * 
     * @return void
     */
    public function testStatsIndex()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Stats Page' . "\n";
        echo '---------------------------' . "\n";

        echo '[#] Test correct render of page (user with no activities/deleted activities)';

        $client = static::createClient(array(), array(
                    'PHP_AUTH_USER' => 'test@test.com',
                    'PHP_AUTH_PW' => 'password',
                ));
        $crawler = $client->request('GET', '/stairs/stats');
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::statsIndexAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'text/html; charset=UTF-8'));
        $this->assertCount(1, $crawler->filter('table'));
        $this->assertEquals(' Stats', $crawler->filter('h4')->text());
        $newcrawler = $crawler->filter('table > tbody');
        $this->assertEquals(0, $newcrawler->children()->children()->eq(0)->text());
        $this->assertEquals(0, $newcrawler->children()->children()->eq(1)->text());
        $this->assertEquals(0, $newcrawler->children()->children()->eq(2)->text());
        $this->assertEquals(0, $newcrawler->children()->children()->eq(3)->text());
        $this->assertEquals(0, $newcrawler->children()->children()->eq(4)->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test correct render of page (user with activities)';

        $client = static::createClient(array(), array(
                    'PHP_AUTH_USER' => 'teststats@team.com',
                    'PHP_AUTH_PW' => 'password',
                ));
        $crawler = $client->request('GET', '/stairs/stats');
        $this->assertCount(1, $crawler->filter('table.table'));
        $newcrawler = $crawler->filter('table > tbody');
        $today = new \Datetime(date('Y-m-d'));
        $seventhday = new \Datetime(date('Y-m-7'));
        $fourteenthday = new \Datetime(date('Y-m-14'));
        if ($today <= $seventhday) {
            $this->assertEquals(610, $newcrawler->children()->children()->eq(0)->text());
            $this->assertEquals(120, $newcrawler->children()->children()->eq(1)->text());
            $this->assertEquals(120, $newcrawler->children()->children()->eq(2)->text());
            $this->assertEquals(121, $newcrawler->children()->children()->eq(3)->text());
            $this->assertEquals(366, $newcrawler->children()->children()->eq(4)->text());
        } elseif ($today > $seventhday && $today <= $fourteenthday) {
            $this->assertEquals(610, $newcrawler->children()->children()->eq(0)->text());
            $this->assertEquals(120, $newcrawler->children()->children()->eq(1)->text());
            $this->assertEquals(241, $newcrawler->children()->children()->eq(2)->text());
            $this->assertEquals(121, $newcrawler->children()->children()->eq(3)->text());
            $this->assertEquals(245, $newcrawler->children()->children()->eq(4)->text());
        } elseif ($today > $fourteenthday) {
            $this->assertEquals(610, $newcrawler->children()->children()->eq(0)->text());
            $this->assertEquals(120, $newcrawler->children()->children()->eq(1)->text());
            $this->assertEquals(363, $newcrawler->children()->children()->eq(2)->text());
            $this->assertEquals(121, $newcrawler->children()->children()->eq(3)->text());
            $this->assertEquals(123, $newcrawler->children()->children()->eq(4)->text());
        }
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test teamIndexAction() function from Stairs Controller
     * Done?
     *  [#] Test correct render of page
     *  [#] Test create new team
     *  [#] Test create new team with name containing spaces in beginning and end
     *  [#] Test create new team with name already taken
     *  [#] Test search team
     *  [#] Test join team
     *  [#] Test joining a non existent team 
     *  [#] Test render individual team page for a non owner member
     *  [#] Test kick member of team for non owner user
     *  [#] Test delete team for non owner user
     *  [#] Test rename team for non owner user
     *  [#] Test assign different owner team for non owner user
     *  [#] Test Leave team
     *  [#] Test render individual team page for owner
     *  [#] Test rename of team
     *  [#] Test kick member of team
     *  [#] Test Leave team while owner
     *  [#] Test assign ownership of team
     *  [#] Test delete team
     * 
     * @return void
     */
    public function testTeamIndexAction()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test team pages' . "\n";
        echo '---------------------------' . "\n";

        echo '[#] Test correct render of page ';

        $client = static::createClient(array(), array(
                    'PHP_AUTH_USER' => 'test@test.com',
                    'PHP_AUTH_PW' => 'password',
                ));
        $crawler = $client->request('GET', '/stairs/team');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamIndexAction', 
                $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'text/html; charset=UTF-8'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(2, $crawler->filter('form'));
        $this->assertEquals(' Your Teams ', $crawler->filter('h4')->text());
        $this->assertEquals('No teams. ', $crawler->filter('table > tbody > tr > td')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test create new team';

        $buttonCrawlerNode = $crawler->selectButton('Create team');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_teamcreatetype[name]' => 'Team123',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamCreateAction', 
                $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertCount(2, $crawler->filter('form'));
        $this->assertEquals(' Your Teams ', $crawler->filter('h4')->text());
        $this->assertEquals(' Team123', $crawler->filter('table > tbody > tr > td')->eq(1)->text());
        $this->assertEquals(' You ', $crawler->filter('table > tbody > tr > td')->eq(2)->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test create new team with name containing spaces in beginning and end';

        $buttonCrawlerNode = $crawler->selectButton('Create team');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_teamcreatetype[name]' => ' Team1235 ',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamCreateAction', 
                $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertCount(2, $crawler->filter('form'));
        $this->assertEquals(' Your Teams ', $crawler->filter('h4')->text());
        $this->assertEquals(' Team1235', $crawler->filter('table > tbody > tr')->eq(1)->filter('td')->eq(1)->text());
        $this->assertEquals(' You ', $crawler->filter('table > tbody > tr')->eq(1)->filter('td')->eq(2)->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test create new team with name already taken';

        $buttonCrawlerNode = $crawler->selectButton('Create team');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_teamcreatetype[name]' => 'Team1234',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamCreateAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertCount(2, $crawler->filter('form'));
        $this->assertEquals('Team name already taken', $crawler->filter('div.alert')->text());
        $this->assertEquals(' Your Teams ', $crawler->filter('h4')->text());
        $this->assertEquals(' Team123', $crawler->filter('table > tbody > tr > td')->eq(1)->text());
        $this->assertEquals(' You ', $crawler->filter('table > tbody > tr > td')->eq(2)->text());
        $this->assertEquals(' Team1235', $crawler->filter('table > tbody > tr')->eq(1)->filter('td')->eq(1)->text());
        $this->assertEquals(' You ', $crawler->filter('table > tbody > tr')->eq(1)->filter('td')->eq(2)->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test search team';

        $buttonCrawlerNode = $crawler->selectButton('Search team');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'name' => '',
                ));
        $this->assertEquals(' Team123', $crawler->filter('table > tbody > tr > td')->eq(1)->text());
        $this->assertEquals(' test@test.com', $crawler->filter('table > tbody > tr > td')->eq(2)->text());
        $this->assertEquals(' Team1234', $crawler->filter('table > tbody > tr')->eq(1)->filter('td')->eq(1)->text());
        $this->assertEquals(' teamowner@team.com', $crawler->filter('table > tbody > tr')->eq(1)->filter('td')->eq(2)->text());
        $this->assertEquals(' Team1235', $crawler->filter('table > tbody > tr')->eq(2)->filter('td')->eq(1)->text());
        $this->assertEquals(' test@test.com', $crawler->filter('table > tbody > tr')->eq(2)->filter('td')->eq(2)->text());
        $this->assertEquals(' Test', $crawler->filter('table > tbody > tr')->eq(3)->filter('td')->eq(1)->text());
        $this->assertEquals(' teamowner@team.com', $crawler->filter('table > tbody > tr')->eq(3)->filter('td')->eq(2)->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test join team';

        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $team_repository = $em->getRepository('IBWWebsiteBundle:Team');
        $team = $team_repository->findOneByName('Test');
        $crawler = $client->request('GET', '/stairs/team/join/' . $team->getId());
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamJoinAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals(' Your Teams ', $crawler->filter('h4')->text());
        $this->assertEquals(' Team123', $crawler->filter('table > tbody > tr > td')->eq(1)->text());
        $this->assertEquals(' You ', $crawler->filter('table > tbody > tr > td')->eq(2)->text());
        $this->assertEquals(' Team1235', $crawler->filter('table > tbody > tr')->eq(1)->filter('td')->eq(1)->text());
        $this->assertEquals(' You ', $crawler->filter('table > tbody > tr')->eq(1)->filter('td')->eq(2)->text());
        $this->assertEquals(' Test', $crawler->filter('table > tbody > tr')->eq(2)->filter('td')->eq(1)->text());
        $this->assertEquals(' teamowner@team.com', $crawler->filter('table > tbody > tr')->eq(2)->filter('td')->eq(2)->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test joining a non existent team ';

        $crawler = $client->request('GET', '/stairs/team/join/13215223');
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamJoinAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isNotFound());
        echo ' . . . Ok' . "\n";

        echo '[#] Test render individual team page for a non owner member';

        $crawler = $client->request('GET', '/stairs/team/' . $team->getId());
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamPageAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'text/html; charset=UTF-8'));
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertCount(7, $crawler->filter('table'));
        $this->assertCount(5, $crawler->filter('h5'));
        $this->assertEquals(' Test', $crawler->filter('table > tbody > tr > td')->eq(1)->text());
        $this->assertEquals(' teamowner@team.com', $crawler->filter('table > tbody > tr > td')->eq(2)->text());
        $this->assertEquals(' Members ', $crawler->filter('h4')->text());
        $this->assertEquals(' teammember@team.com', $crawler->filter('table')->eq(1)->filter('tbody > tr > td')->eq(1)->text());
        $this->assertEquals(' teamowner@team.com', 
                    $crawler->filter('table')->eq(1)->filter('tbody > tr')->eq(1)->filter('td')->eq(1)->text());
        $this->assertEquals(' test@test.com', 
                    $crawler->filter('table')->eq(1)->filter('tbody > tr')->eq(2)->filter('td')->eq(1)->text());
        $this->assertEquals(' Tops', $crawler->filter('h4')->eq(1)->text());
        $this->assertEquals('All time', $crawler->filter('h5')->eq(0)->text());
        $this->assertEquals('This week', $crawler->filter('h5')->eq(1)->text());
        $this->assertEquals('This month', $crawler->filter('h5')->eq(2)->text());
        $this->assertEquals('Last week', $crawler->filter('h5')->eq(3)->text());
        $this->assertEquals('Last month', $crawler->filter('h5')->eq(4)->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test kick member of team for non owner user';

        $user_repository = $em->getRepository('IBWWebsiteBundle:User');
        $user = $user_repository->findOneByEmail('teammember@team.com');
        $crawler = $client->request('GET', '/stairs/team/' . $team->getId() . '/kick/' . $user->getId());
        $this->assertTrue($client->getResponse()->isNotFound());
        echo ' . . . Ok' . "\n";

        echo '[#] Test delete team for non owner user';

        $crawler = $client->request('GET', '/stairs/team/delete/' . $team->getId());
        $this->assertTrue($client->getResponse()->isNotFound());
        echo ' . . . Ok' . "\n";

        echo '[#] Test rename team for non owner user';

        $crawler = $client->request('GET', '/stairs/team/rename/' . $team->getId());
        $this->assertTrue($client->getResponse()->isNotFound());
        echo ' . . . Ok' . "\n";

        echo '[#] Test assign different owner team for non owner user';

        $user_repository = $em->getRepository('IBWWebsiteBundle:User');
        $user = $user_repository->findOneByEmail('test@test.com');
        $crawler = $client->request('GET', '/stairs/team/' . $team->getId() . '/owner/' . $user->getId());
        $this->assertTrue($client->getResponse()->isNotFound());
        echo ' . . . Ok' . "\n";

        echo '[#] Test Leave team';

        $crawler = $client->request('GET', '/stairs/team/leave/' . $team->getId());
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamLeaveAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamIndexAction',
                    $client->getRequest()->attributes->get('_controller'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test render individual team page for owner';

        $team = $team_repository->findOneByName('Team123');
        $crawler = $client->request('POST', '/api/team/join',
                array('email' => 'teammember@team.com', 'password' => 'password', 'id' => $team->getId()));
        $crawler = $client->request('GET', '/stairs/team/' . $team->getId());
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamPageAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'text/html; charset=UTF-8'));
        $this->assertCount(5, $crawler->filter('form'));
        $this->assertCount(7, $crawler->filter('table'));
        $this->assertCount(5, $crawler->filter('h5'));
        $this->assertEquals(' Team123', $crawler->filter('table')->eq(0)->filter('tbody > tr > td')->eq(1)->text());
        $this->assertEquals(' test@test.com', $crawler->filter('table')->eq(1)->filter('tbody > tr > td')->eq(1)->text());
        $this->assertEquals(' You are owner', $crawler->filter('table')->eq(1)->filter('tbody > tr > td')->eq(2)->text());
        $this->assertEquals(' teammember@team.com', 
                    $crawler->filter('table')->eq(1)->filter('tbody > tr')->eq(1)->filter('td')->eq(1)->text());
        $this->assertEquals('Assign Ownership', $crawler->filter('table')->eq(1)->filter('tbody > tr')
                        ->eq(1)->filter('td')->eq(2)->filter('form > button')->text());
        $this->assertEquals('×', $crawler->filter('table')->eq(1)->filter('tbody > tr')
                        ->eq(1)->filter('td')->eq(3)->filter('form > button')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test rename of team';

        $buttonCrawlerNode = $crawler->selectButton('Change name');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'ibw_websitebundle_teamcreatetype[name]' => 'changename',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamRenameAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamPageAction',    
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertGreaterThan(2, $crawler->filter('form')->count());
        $this->assertCount(7, $crawler->filter('table'));
        $this->assertCount(5, $crawler->filter('h5'));
        $this->assertEquals(' changename', $crawler->filter('table')->eq(0)->filter('tbody > tr > td')->eq(1)->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test kick member of team';

        $client2 = static::createClient();
        $client2->request('POST', '/api/team/join', array('email' => 'teammember2@team.com', 'password' => 'password',
            'id' => $team->getId()));
        $user_repository = $em->getRepository('IBWWebsiteBundle:User');
        $user = $user_repository->findOneByEmail('teammember@team.com');
        $crawler = $client->request('GET', '/stairs/team/' . $team->getId() . '/kick/' . $user->getId());
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamKickAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamPageAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('User has been kicked', $crawler->filter('div.alert')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test Leave team while owner';

        $buttonCrawlerNode = $crawler->selectButton('Leave Team');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form);
        $this->assertEquals(
                'IBW\WebsiteBundle\Controller\StairsController::teamLeaveAction', 
                    $client->getRequest()->attributes->get('_controller')
        );
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('You cannot leave the team until you assign another owner', 
                    $crawler->filter('div.alert')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test assign ownership of team';
        $client2 = static::createClient();
        $client2->request('POST', '/api/team/join', 
                    array('email' => 'teammember2@team.com', 'password' => 'password', 'id' => $team->getId()));
        $buttonCrawlerNode = $crawler->selectButton('Assign Ownership');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form);
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamOwnerAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamPageAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertEquals('Owner has been changed', $crawler->filter('div.alert')->text());
        $em->refresh($team);
        $this->assertEquals($team->getOwner()->getEmail(), 'teammember2@team.com');
        echo ' . . . Ok' . "\n";

        echo '[#] Test delete team';
        $client = static::createClient(array(), array(
                    'PHP_AUTH_USER' => 'teamowner@team.com',
                    'PHP_AUTH_PW' => 'password',
                ));
        $team = $team_repository->findOneByName('Test');
        $crawler = $client->request('GET', '/stairs/team/' . $team->getId());
        $buttonCrawlerNode = $crawler->selectButton('Delete Team');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form);
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamDeleteAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::teamIndexAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('Team deleted', $crawler->filter('div.alert')->text());
        $em->refresh($team);
        $team = $team_repository->findOneById($team->getId());
        $this->assertNull($team);
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test userSettingsAction() function from Stairs Controller
     * Done?
     *  [#] Test correct render of page
     *  [#] Test correct change of password
     *  [#] Test change of password with field empty
     * 
     * @return void
     */
    public function testUserSettingsAction()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Account settings pages' . "\n";
        echo '---------------------------' . "\n";

        $client = static::createClient(array(), array(
                    'PHP_AUTH_USER' => 'test@test.com',
                    'PHP_AUTH_PW' => 'password',
                ));

        echo '[#] Test correct render of page ';

        $crawler = $client->request('GET', '/stairs/settings');
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::userSettingsAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'text/html; charset=UTF-8'));
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertEquals(' Account Settings ', $crawler->filter('h4')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test correct change of password ';

        $buttonCrawlerNode = $crawler->selectButton('Submit');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'password' => 'password123',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::userSettingsAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->setServerParameter('PHP_AUTH_PW', 'password123');
        $crawler = $client->followRedirect();
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::userSettingsAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('User updated!', $crawler->filter('div.alert')->text());
        echo ' . . . Ok' . "\n";

        echo '[#] Test if user is edited in database';

        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('IBWWebsiteBundle:User')->findOneByEmail('test@test.com');
        $factory = $kernel->getContainer()->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $new_password = $encoder->encodePassword('password123', $user->getSalt());
        $this->assertSame($user->getPassword(), $new_password);
        echo ' . . . Ok' . "\n";

        echo '[#] Test change of password with field empty';

        $buttonCrawlerNode = $crawler->selectButton('Submit');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'password' => '',
                ));
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::userSettingsAction', 
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertFalse($client->getResponse()->isRedirect());
        $this->assertEquals('Password required', $crawler->filter('div.alert')->text());
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test topIndexAction() function from Stairs Controller
     * Done?
     *  [#] Test correct render of page
     *  [#] Test correct change of password
     *  [#] Test change of password with field empty
     * 
     * @return void
     */
    public function testTopIndexAction()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Top Index Test' . "\n";
        echo '---------------------------' . "\n";

        $client = static::createClient(array(), array(
                    'PHP_AUTH_USER' => 'test@test.com',
                    'PHP_AUTH_PW' => 'password123',
                ));

        echo '[#] Test correct render of page ';

        $crawler = $client->request('GET', '/stairs/top');
        $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::topIndexAction',
                    $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'text/html; charset=UTF-8'));
        $this->assertCount(1, $crawler->filter('form'));
        $this->assertCount(1, $crawler->filter('h4:contains("Tops")'));
        $newcrawler = $crawler->filter('table.center-table');
        $today = new \Datetime(date('Y-m-d'));
        $seventhday = new \Datetime(date('Y-m-7'));
        $fourteenthday = new \Datetime(date('Y-m-14'));
        //All time
        $this->assertEquals(' teststats@team.com ', $newcrawler->eq(0)->children()->eq(0)->children()->eq(1)->text());
        $this->assertEquals('610 ', $newcrawler->eq(0)->children()->eq(0)->children()->eq(2)->text());
        $this->assertEquals(' testtop@team.com ', $newcrawler->eq(0)->children()->eq(1)->children()->eq(1)->text());
        $this->assertEquals('531 ', $newcrawler->eq(0)->children()->eq(1)->children()->eq(2)->text());
        if ($today <= $seventhday) {
            //This week
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(1)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('120 ', $newcrawler->eq(1)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(1)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('110 ', $newcrawler->eq(1)->children()->eq(1)->children()->eq(2)->text());
            //This month
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(2)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('120 ', $newcrawler->eq(2)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(2)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('110 ', $newcrawler->eq(2)->children()->eq(1)->children()->eq(2)->text());
            //Last week
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(3)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('130 ', $newcrawler->eq(3)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(3)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('121 ', $newcrawler->eq(3)->children()->eq(1)->children()->eq(2)->text());
            //Last month
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(4)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('366 ', $newcrawler->eq(4)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(4)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('300 ', $newcrawler->eq(4)->children()->eq(1)->children()->eq(2)->text());
        } elseif ($today > $seventhday && $today <= $fourteenthday) {
            //This week
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(1)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('120 ', $newcrawler->eq(1)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(1)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('110 ', $newcrawler->eq(1)->children()->eq(1)->children()->eq(2)->text());
            //This month
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(2)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('241 ', $newcrawler->eq(2)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(2)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('240 ', $newcrawler->eq(2)->children()->eq(1)->children()->eq(2)->text());
            //Last week
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(3)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('130 ', $newcrawler->eq(3)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(3)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('121 ', $newcrawler->eq(3)->children()->eq(1)->children()->eq(2)->text());
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(4)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('245 ', $newcrawler->eq(4)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(4)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('170 ', $newcrawler->eq(4)->children()->eq(1)->children()->eq(2)->text());
        } elseif ($today > $fourteenthday) {
            //This week
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(1)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('120 ', $newcrawler->eq(1)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(1)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('110 ', $newcrawler->eq(1)->children()->eq(1)->children()->eq(2)->text());
            //This month
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(2)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('390 ', $newcrawler->eq(2)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(2)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('363 ', $newcrawler->eq(2)->children()->eq(1)->children()->eq(2)->text());
            //Last week
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(3)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('130 ', $newcrawler->eq(3)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(3)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('121 ', $newcrawler->eq(3)->children()->eq(1)->children()->eq(2)->text());
            //Last month
            $this->assertEquals(' teststats@team.com ', $newcrawler->eq(4)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('123 ', $newcrawler->eq(4)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals(' testtop@team.com ', $newcrawler->eq(4)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('20 ', $newcrawler->eq(4)->children()->eq(1)->children()->eq(2)->text());
        }
        echo ' . . . Ok' . "\n";

        echo '[#] Test custom period ';

        $buttonCrawlerNode = $crawler->selectButton('Get Top');
        $form = $buttonCrawlerNode->form();
        $crawler = $client->submit($form, array(
            'start_date' => date('Y-m-d', strtotime('-3 day', time())),
            'end_date' => date('Y-m-d', strtotime('Tomorrow', time())),
                ));
        if (date('Y-m-d') <= date('Y-m-7')) {
            $this->assertEquals('IBW\WebsiteBundle\Controller\StairsController::topIndexAction', 
                    $client->getRequest()->attributes->get('_controller'));
            $this->assertTrue($client->getResponse()->isSuccessful());
            $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'text/html; charset=UTF-8'));
            $this->assertGreaterThan(4, $crawler->filter('table')->count());
            $this->assertCount(1, $crawler->filter('form'));
            $this->assertCount(1, $crawler->filter('h5:contains("Custom period")'));
            $newcrawler = $crawler->filter('table.center-table');
            //Custom testtop@team.com
            $this->assertEquals('teststats@team.com', $newcrawler->eq(0)->children()->eq(0)->children()->eq(1)->text());
            $this->assertEquals('120 ', $newcrawler->eq(0)->children()->eq(0)->children()->eq(2)->text());
            $this->assertEquals('testtop@team.com', $newcrawler->eq(0)->children()->eq(1)->children()->eq(1)->text());
            $this->assertEquals('110 ', $newcrawler->eq(0)->children()->eq(1)->children()->eq(2)->text());
        }
        $kernel = static::createKernel();
        $kernel->boot();
        DatabaseInit::databaseClear($kernel);
    }

}
