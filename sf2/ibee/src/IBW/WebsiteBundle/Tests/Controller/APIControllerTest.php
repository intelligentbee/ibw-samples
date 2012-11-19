<?php

namespace IBW\WebsiteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use IBW\WebsiteBundle\Entity\User;
use IBW\WebsiteBundle\Entity\Team;
use IBW\WebsiteBundle\Entity\UserTeam;
use IBW\WebsiteBundle\Entity\StairsActivity;

/**
 * Functional tests for APIController.php
 */
class APIControllerTest extends WebTestCase
{

    /**
     * Helper functon that clears test database
     * 
     * @return void
     */
    public static function databaseClear()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Deleting database' . "\n";
        echo '---------------------------' . "\n";
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $users = $em->getRepository('IBWWebsiteBundle:User')->findAll();
        foreach ($users as $user) {
            $em->remove($user);
        }
        $teams = $em->getRepository('IBWWebsiteBundle:Team')->findAll();
        $user_teams = $em->getRepository('IBWWebsiteBundle:UserTeam')->findAll();
        foreach ($teams as $team) {
            $em->remove($team);
        }
        foreach ($user_teams as $user_team) {
            $em->remove($user_team);
        }
        $activities = $em->getRepository('IBWWebsiteBundle:StairsActivity')->findAll();
        foreach ($activities as $activity) {
            $em->remove($activity);
        }
        $repository = $em->getRepository('IBWWebsiteBundle:GcmDevice');
        $gcms = $repository->findAll();
        foreach ($gcms as $gcm) {
            $em->remove($gcm);
        }
        $em->flush();
    }

    /**
     * Populates test database
     * 
     * @return void
     */
    public static function databasePopulation()
    {
        self::databaseClear();
        echo "\n" . '---------------------------' . "\n";
        echo 'Populating database' . "\n";
        echo '---------------------------' . "\n";
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $users_array = array();
        $users = array(
            array('email' => 'test@intelligentbee.com', 'password' => 'password'),
            array('email' => 'test@test.com', 'password' => 'password'),
            array('email' => 'stats@test.com', 'password' => 'password'),
            array('email' => 'stats2@test.com', 'password' => 'password'),
        );
        foreach ($users as $usr) {
            $user = new User();
            $user->setEmail($usr['email']);
            $factory = $kernel->getContainer()->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($usr['password'], $user->getSalt());
            $user->setPassword($password);
            array_push($users_array, $user);
            $em->persist($user);
        }
        $stair_activities = array(
            array('user' => $users_array[0], 'amount' => 2, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 0, 'created_at' => '2012-10-12 05:18:14'),
            array('user' => $users_array[0], 'amount' => 2, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 0, 'created_at' => '2012-10-13 05:18:14'),
            array('user' => $users_array[0], 'amount' => 2, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 0, 'created_at' => '2012-10-14 05:18:14'),
            array('user' => $users_array[0], 'amount' => 3, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 0, 'created_at' => '2012-10-15 05:18:14'),
            array('user' => $users_array[0], 'amount' => 3, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 0, 'created_at' => '2012-10-16 05:18:14'),
            array('user' => $users_array[1], 'amount' => 2, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 0, 'created_at' => '2012-10-12 05:18:14'),
            array('user' => $users_array[1], 'amount' => 2, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 0, 'created_at' => '2012-10-13 05:18:14'),
            array('user' => $users_array[1], 'amount' => 2, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 0, 'created_at' => '2012-10-14 05:18:14'),
            array('user' => $users_array[1], 'amount' => 1, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 0, 'created_at' => '2012-10-15 05:18:14'),
            array('user' => $users_array[1], 'amount' => 1, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 0, 'created_at' => '2012-10-16 05:18:14'),
            array('user' => $users_array[2], 'amount' => 2, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 0, 'created_at' => '2012-10-16 05:18:14'),
            array('user' => $users_array[2], 'amount' => 2, 'lat' => null, 'lng' => null, 'is_notification_sent' => 1,
                'is_deleted' => 1, 'created_at' => '2012-10-16 05:18:14'),
        );
        foreach ($stair_activities as $stairs_activity) {
            $stairsActivity = new StairsActivity();
            $stairsActivity->setUser($stairs_activity['user']);
            $stairsActivity->setAmount($stairs_activity['amount']);
            $stairsActivity->setLat($stairs_activity['lat']);
            $stairsActivity->setLng($stairs_activity['lng']);
            $stairsActivity->setIsNotificationSent($stairs_activity['is_notification_sent']);
            $stairsActivity->setIsDeleted($stairs_activity['is_deleted']);
            $stairsActivity->setCreatedAt(new \Datetime($stairs_activity['created_at']));
            $em->persist($stairsActivity);
        }
        $tms = array();
        $teams = array(
            array('name' => 'Test', 'owner' => $users_array[0]),
            array('name' => 'TestStats', 'owner' => $users_array[3]),
        );
        foreach ($teams as $team) {
            $tm = new Team();
            $tm->setName($team['name']);
            $tm->setOwner($team['owner']);
            $userTeam = new UserTeam();
            $userTeam->setTeam($tm);
            $userTeam->setUser($team['owner']);
            array_push($tms, $tm);
            $em->persist($userTeam);
            $em->persist($tm);
        }
        $user_teams = array(
            array('team' => $tms[0], 'user' => $users_array[2]),
        );
        $usr_tms = array();
        foreach ($user_teams as $user_team) {
            $userTeam = new UserTeam();
            $userTeam->setTeam($user_team['team']);
            $userTeam->setUser($user_team['user']);
            array_push($usr_tms, $tm);
            $em->persist($userTeam);
        }
        $em->flush();
    }

    /**
     * Test createUserAction() function from API Controller
     * Done?
     *  [#] Test succesfull creation user
     *  [#] Test user created exists in database
     *  [#] Test for creating a user that is allready existent
     *  [#] Test for creating a user with an email that is allready used
     *  [#] Test for password too short
     *  [#] Test for email parameter not email type
     *  [#] Test for email parameter left blank
     *  [#] Test for password parameter left blank
     *  [#] Test for email & password parameters left blank
     * 
     * @return void
     */
    public function testCreateUser()
    {
        self::databasePopulation();
        echo "\n" . '------------------' . "\n";
        echo 'Test Create User' . "\n";
        echo '------------------' . "\n";

        echo '[#] Test succesfull creation of user';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/user/create', array('email' => 'create_test@intelligentbee.com', 'password' => 'password'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'), 'Response is not JSON type');
        $json = json_decode($client->getResponse()->getContent());
        $this->assertFalse($json == NULL, 'Null JSON returned');
        $this->assertObjectHasAttribute('result', $json, 'No result attribute');
        $this->assertObjectHasAttribute('message', $json, 'No message attribute');
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_user_created'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test user created exists in database';

        $user = new User();
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('IBWWebsiteBundle:User')->findOneByEmail('test@intelligentbee.com');
        $this->assertNotNull($user);
        echo ' . . . Ok' . "\n";

        echo '[#] Test for creating a user that is allready existent';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/user/create', array('email' => 'create_test@intelligentbee.com', 'password' => 'password'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_exists'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_user_allready_created'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for creating a user with an email that is allready used';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/user/create', array('email' => 'create_test@intelligentbee.com', 'password' => 'passsdfsdfs'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Email already used ");
        $user = $em->getRepository('IBWWebsiteBundle:User')->findOneByEmail($client->getRequest()->request->get('email'));
        $em->remove($user);
        $em->flush();
        echo ' . . . Ok' . "\n";

        echo '[#] Test for password too short ';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/user/create', array('email' => 'create_test@intelligentbee.com', 'password' => 'pass'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Password too short Enter at least 6 characters ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email parameter not email type';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/user/create', array('email' => 'email.com', 'password' => 'password'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Not a valid email ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email parameter left blank';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/user/create', array('email' => '', 'password' => 'password'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Email required ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for password parameter left blank';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/user/create', array('email' => 'create_test@intelligentbee.com', 'password' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Password required ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email & password parameters left blank';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/user/create', array('email' => '', 'password' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Email required Password required ");
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test createStairActivityAction() function from API Controller
     * Done?
     *  [#] Test succesfull creation of activity
     *  [#] Test if activity created exists in database
     *  [#] Test for wrong password
     *  [#] Test for amount parameter left blank
     *  [#] Test for amount parameter is 0
     *  [#] Test for creation with a non existent/wrong email
     *  [#] Test for email & password left blank
     *  [#] Test for all parameters left blank
     *  [#] Test for invalid date
     *  [ ] Test for email parameter left blank
     *  [ ] Test for email parameter left blank
     * 
     * @return void
     */
    public function testCreateStairActivity()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Create StairActivity' . "\n";
        echo '---------------------------' . "\n";
        echo '[#] Test succesfull creation of activity';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/stairactivity/create', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'amount' => 120));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('id', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activity_created'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test if activity created exists in database';

        $stairActivity = new StairsActivity();
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $stairActivity = $em->getRepository('IBWWebsiteBundle:StairsActivity')->findOneById($json->id);
        $this->assertNotNull($stairActivity);
        $user_repository = $em->getRepository('IBWWebsiteBundle:User');
        $user = $user_repository->findOneByEmail($client->getRequest()->request->get('email'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong password';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/stairactivity/create', array('email' => 'test@intelligentbee.com', 'password' => 'gibberish', 'amount' => 120));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for amount parameter left blank';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/stairactivity/create', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'amount' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Amount required ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for amount parameter is 0';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/stairactivity/create', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'amount' => 0));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Amount must greater than 0 ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for creation with a non existent/wrong email ';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/stairactivity/create', array('email' => "test_email@rmail.com", 'password' => 'password', 'amount' => 120));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email & password left blank';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/stairactivity/create', array('email' => '', 'password' => '', 'amount' => 23));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for all parameters left blank';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/stairactivity/create', array('email' => '', 'password' => '', 'amount' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for invalid date';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/stairactivity/create', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'amount' => 120, 'created_at' => 'blablabla'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        //$this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        $em->remove($stairActivity);
        $em->flush();
    }

    /**
     * Test editUserAction() function from API Controller
     * Done?
     *  [#] Test succesfull edit of user
     *  [#] Test if user is edited in database
     *  [#] Test for non existent email/wrong email
     *  [#] Test for wrong password
     *  [#] Test for password too short
     *  [#] Test for new_password parameter left blank
     *  [#] Test for email parameter left blank
     *  [#] Test for password parameter left blank
     *  [#] Test for email & password parameter left blank
     *  [#] Test for all parameters left blank
     * 
     * @return void
     */
    public function testUserEdit()
    {
        echo "\n" . '------------------' . "\n";
        echo 'Test Edit User' . "\n";
        echo '------------------' . "\n";
        echo '[#] Test succesfull edit of user';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/edit', array('email' => 'test@intelligentbee.com',
            'password' => 'password', 'new_password' => 'password2'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertFalse($json == NULL);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_user_updated'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test if user is edited in database';

        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('IBWWebsiteBundle:User')->findOneByEmail($client->getRequest()->request->get('email'));
        $factory = $kernel->getContainer()->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $new_password = $encoder->encodePassword($client->getRequest()->request->get('new_password'), $user->getSalt());
        $this->assertSame($user->getPassword(), $new_password);
        echo ' . . . Ok' . "\n";

        echo '[#] Test for non existent email/wrong email';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/edit', array('email' => "test_email@eurhe.com",
            'password' => 'password', 'new_password' => 'password2'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong password ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/edit', array('email' => 'test@intelligentbee.com',
            'password' => 'gibberish', 'new_password' => 'password2'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for password too short ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/edit', array('email' => 'test@intelligentbee.com',
            'password' => 'password2', 'new_password' => 'pass'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Password too short Enter at least 6 characters ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for new_password parameter left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/edit', array('email' => 'test@intelligentbee.com',
            'password' => 'password2', 'new_password' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Password required ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email parameter left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/edit', array('email' => '',
            'password' => 'password2', 'new_password' => 'gibberish'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for password parameter left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/edit', array('email' => 'test@intelligentbee.com',
            'password' => '', 'new_password' => 'gibberish'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email & password parameter left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/edit', array('email' => '',
            'password' => '', '' => 'gibberish'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for all parameters left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/edit', array('email' => '',
            'password' => '', 'new_password' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        $crawler = $client->request('POST', '/api/user/edit', array('email' => 'test@intelligentbee.com',
            'password' => 'password2', 'new_password' => 'password'));
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test editStairActivityAction() function from API Controller
     * Done?
     *  [#] Test succesfull edit of activity
     *  [#] Test if activity created exists in database
     *  [#] Test for creation with a non existent/wrong email
     *  [#] Test for wrong password
     *  [#] Test for email & password left blank
     *  [#] Test for amount parameter left blank
     *  [#] Test for amount parameter is 0
     *  [#] Test for edit of activity with non existent id
     *  [#] Test for id left blank
     *  [#] Test for amount is negative number
     *  [#] Test for user tring to modify a activity that doesn't belong to him
     * 
     * @return void
     */
    public function testStairActivityEdit()
    {

        echo "\n" . '------------------------' . "\n";
        echo 'Test Edit StairActivity' . "\n";
        echo '------------------------' . "\n";
        echo '[#] Test succesfull edit of activity ';
        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/stairactivity/create', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'amount' => 120));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $json);
        $crawler = $client->request('POST', '/api/stairactivity/edit', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'id' => $json->id, 'amount' => 122));
        $json = json_decode($client->getResponse()->getContent());
        $id = $json->id;
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('id', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activity_updated'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test if activity created exists in database';

        $stairActivity = new StairsActivity();
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $stairActivity = $em->getRepository('IBWWebsiteBundle:StairsActivity')->findOneById($json->id);
        $this->assertNotNull($stairActivity);
        $this->assertEquals($stairActivity->getAmount(), $client->getRequest()->request->get('amount'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for creation with a non existent/wrong email';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/edit', array('email' => 'test@test.com', 'password' => 'gibberish', 'id' => $json->id, 'amount' => 133));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong password';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/edit', array('email' => 'test@intelligentbee.com', 'password' => 'gibberish', 'id' => $id, 'amount' => 133));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email & password left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/edit', array('email' => '', 'password' => '', 'id' => $id, 'amount' => 133));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for amount parameter left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/edit', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'id' => $id, 'amount' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Amount required ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for amount parameter is 0 ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/edit', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'id' => $id, 'amount' => 0));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Amount must greater than 0 ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for edit of activity with non existent id';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/edit', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'id' => 123124, 'amount' => 134));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_no_activity_found'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for id left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/edit', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'id' => '', 'amount' => 134));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_no_activity_found'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for amount is negative number';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/edit', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'id' => $id, 'amount' => -134));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Amount must greater than 0 ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for user tring to modify a activity that doesnt belong to him';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/user/create', array('email' => 'test1@test.com', 'password' => 'password'));
        $crawler = $client->request('POST', '/api/stairactivity/edit', array('email' => 'test1@test.com', 'password' => 'password', 'id' => $id, 'amount' => 134));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_no_activity_found'));
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test deleteStairActivityAction() function from API Controller
     * Done?
     *  [#] Test succesfull deletion of activity
     *  [#] Test succesfull deletion of activity in database
     *  [#] Test for creation with a non existent/wrong email
     *  [#] Test for wrong password
     *  [#] Test for email & password left blank
     *  [#] Test for id left blank
     *  [#] Test for user tring to delete a activity that doesn't belong to him
     *  [#] Test for email parameter left blank
     *  [#] Test for delete with a non-existend id
     * 
     * @return void
     */
    public function testStairActivityDelete()
    {
        echo "\n" . '-------------------------' . "\n";
        echo 'Test DeleteStairActivity' . "\n";
        echo '-------------------------' . "\n";

        echo '[#] Test succesfull deletion of activity ';
        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/user/create', array('email' => 'test@intelligentbee.com', 'password' => 'password'));
        $crawler = $client->request('PUT', '/api/stairactivity/create', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'amount' => 120));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $json);
        $id = $json->id;
        $crawler = $client->request('DELETE', '/api/stairactivity/delete', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'id' => $id));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activity_deletion'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull deletion of activity in database ';

        $stairActivity = new StairsActivity();
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $stairActivity = $em->getRepository('IBWWebsiteBundle:StairsActivity')->findOneById($id);
        $this->assertNotNull($stairActivity);
        $this->assertTrue($stairActivity->getIsDeleted());
        echo ' . . . Ok' . "\n";

        echo '[#] Test for deletion with a non existent/wrong email';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/delete', array('email' => 'testasdasda@test.com', 'password' => 'gibberish', 'id' => $id));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong password';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/delete', array('email' => 'test@intelligentbee.com', 'password' => 'gibberish', 'id' => $id));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email & password left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/delete', array('email' => '', 'password' => '', 'id' => $id));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for delete of activity with non existent id';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/delete', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'id' => 123124));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_no_activity_found'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for id left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/delete', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'id' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_no_activity_found'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for user tring to delete a activity that doesnt belong to him';

        $client = static::createClient();
        $crawler = $client->request('PUT', '/api/user/create', array('email' => 'test@test.com', 'password' => 'password'));
        $crawler = $client->request('POST', '/api/stairactivity/delete', array('email' => 'test@test.com', 'password' => 'password', 'id' => $id));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_no_activity_found'));
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test testGetStairActivityAction() function from API Controller
     * Done?
     *  [#] Test succesfull return of activity without limit
     *  [#] Test succesfull return of activity with limit
     *  [#] Test succesfull return of activity with limit negative
     *  [#] Test for non existent email/wrong email
     *  [#] Test for wrong password
     *  [#] Test for email & password left blank
     * 
     * @return void
     */
    public function testGetStairActivity()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Get StairActivity' . "\n";
        echo '---------------------------' . "\n";

        echo '[#] Test succesfull return of activity without limit';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/get', array('email' => 'test@intelligentbee.com', 'password' => 'password'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('activities', $json);
        foreach ($json->activities as $activity) {
            $this->assertObjectHasAttribute('id', $activity);
            $this->assertObjectHasAttribute('amount', $activity);
            $this->assertObjectHasAttribute('created_at', $activity);
            $this->assertObjectHasAttribute('lng', $activity);
            $this->assertObjectHasAttribute('lat', $activity);
        }
        $this->assertTrue(count($json->activities) == 6);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of activity with limit';

        $crawler = $client->request('POST', '/api/stairactivity/get', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'limit' => 3));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('activities', $json);
        foreach ($json->activities as $activity) {
            $this->assertObjectHasAttribute('id', $activity);
            $this->assertObjectHasAttribute('amount', $activity);
            $this->assertObjectHasAttribute('created_at', $activity);
            $this->assertObjectHasAttribute('lng', $activity);
            $this->assertObjectHasAttribute('lat', $activity);
        }
        $this->assertTrue(count($json->activities) == 3);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of activity with limit negative';

        $crawler = $client->request('POST', '/api/stairactivity/get', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'limit' => -3));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('activities', $json);
        foreach ($json->activities as $activity) {
            $this->assertObjectHasAttribute('id', $activity);
            $this->assertObjectHasAttribute('amount', $activity);
            $this->assertObjectHasAttribute('created_at', $activity);
            $this->assertObjectHasAttribute('lng', $activity);
            $this->assertObjectHasAttribute('lat', $activity);
        }
        $this->assertTrue(count($json->activities) == 3);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for non existent email/wrong email';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/get', array('email' => 'test_email@fjd.com', 'password' => 'password', 'limit' => 3));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong password ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/get', array('email' => 'test@intelligentbee.com', 'password' => 'gibberish', 'limit' => 3));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email & password left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/get', array('email' => '', 'password' => '', 'limit' => 3));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test testGetTop() function from API Controller
     * Done?
     *  [#] Test succesfull return of activity without limit
     *  [#] Test succesfull return of activity with limit
     *  [#] Test succesfull return of activity with start and end date
     *  [#] Test succesfull return of activity with limit & start_date & end_date
     *  [#] Test succesfull return of activity with start date and no end date 
     *  [#] Test succesfull return of activity with end and no start date
     * 
     * @return void
     */
    public function testGetTop()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Get TOP' . "\n";
        echo '---------------------------' . "\n";
        $created_at1 = '2012-10-12 05:18:14';
        $created_at2 = '2012-10-13 05:18:14';
        $created_at3 = '2012-10-14 05:18:14';
        $created_at4 = '2012-10-15 05:18:14';
        $created_at5 = '2012-10-16 05:18:14';

        echo '[#] Test succesfull return of activity without limit';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/stairactivity/top', array('start_date' => '', 'end_date' => ''));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('top', $json);
        foreach ($json->top as $activity) {
            $this->assertObjectHasAttribute('email', $activity);
            $this->assertObjectHasAttribute('total', $activity);
        }
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of activity with limit';

        $crawler = $client->request('POST', '/api/stairactivity/top', array('start_date' => '', 'end_date' => '', 'limit' => 1));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('top', $json);
        foreach ($json->top as $activity) {
            $this->assertObjectHasAttribute('email', $activity);
            $this->assertObjectHasAttribute('total', $activity);
        }
        $this->assertTrue(count($json->top) == 1);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of activity with start and end date';

        $crawler = $client->request('POST', '/api/stairactivity/top', array('start_date' => $created_at1, 'end_date' => $created_at3));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('top', $json);
        foreach ($json->top as $activity) {
            $this->assertObjectHasAttribute('email', $activity);
            $this->assertObjectHasAttribute('total', $activity);
            $this->assertTrue($activity->total == 6);
        }
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of activity with limit start and end date';

        $crawler = $client->request('POST', '/api/stairactivity/top', array('start_date' => $created_at1, 'end_date' => $created_at3, 'limit' => 1));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('top', $json);
        foreach ($json->top as $activity) {
            $this->assertObjectHasAttribute('email', $activity);
            $this->assertObjectHasAttribute('total', $activity);
            $this->assertTrue($activity->total == 6);
        }
        $this->assertTrue(count($json->top) == 1);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of activity with start date and no end date';

        $crawler = $client->request('POST', '/api/stairactivity/top', array('start_date' => $created_at4, 'end_date' => ''));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('top', $json);
        foreach ($json->top as $activity) {
            $this->assertObjectHasAttribute('email', $activity);
            $this->assertObjectHasAttribute('total', $activity);
            $this->assertTrue($activity->total == 2 || $activity->total == 128);
        }
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of activity with end and no start date';

        $crawler = $client->request('POST', '/api/stairactivity/top', array('start_date' => '', 'end_date' => $created_at3));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('top', $json);
        foreach ($json->top as $activity) {
            $this->assertObjectHasAttribute('email', $activity);
            $this->assertObjectHasAttribute('total', $activity);
            $this->assertTrue($activity->total == 6);
        }
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test testGetUserStats() function from API Controller
     * Done?
     *  [#] Test succesfull return of stats without dates
     *  [#] Test succesfull return of stats with dates
     *  [#] Test succesfull return of stats with start_date
     *  [#] Test succesfull return of stats with end date
     *  [#] Test for returning stats with deleted activities
     *  [#] Test if user has no activities - must return 0
     *  [#] Test for non existent email/wrong email
     *  [#] Test for wrong password
     *  [#] Test for email & password left blank
     * 
     * @return void
     */
    public function testGetUserStats()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Get User stats' . "\n";
        echo '---------------------------' . "\n";
        $created_at1 = '2012-10-12 05:18:14';
        $created_at2 = '2012-10-13 05:18:14';
        $created_at3 = '2012-10-14 05:18:14';
        $created_at4 = '2012-10-15 05:18:14';
        $created_at5 = '2012-10-16 05:18:14';
        $client = static::createClient();
        echo '[#] Test succesfull return of stats without dates';

        $crawler = $client->request('POST', '/api/user/stats', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'start_date' => '', 'end_date' => ''));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        $this->assertTrue($json->total == 134);
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of stats with dates';

        $crawler = $client->request('POST', '/api/user/stats', array('email' => 'test@intelligentbee.com', 'password' => 'password',
            'start_date' => $created_at2, 'end_date' => $created_at4));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        $this->assertTrue($json->total == 7);
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of stats with start_date';

        $crawler = $client->request('POST', '/api/user/stats', array('email' => 'test@intelligentbee.com', 'password' => 'password',
            'start_date' => $created_at2, 'end_date' => ''
                ));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        $this->assertTrue($json->total == 132);
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of stats with end date';

        $crawler = $client->request('POST', '/api/user/stats', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'start_date' => '',
            'end_date' => $created_at4
                ));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        $this->assertTrue($json->total == 9);
        echo ' . . . Ok' . "\n";

        echo '[#] Test for non existent email/wrong email';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/stats', array('email' => 'test_email@dashd.com', 'password' => 'password', 'start_date' => '',
            'end_date' => $created_at4));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong password ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/stats', array('email' => 'test@intelligentbee.com', 'password' => 'gibberish', 'start_date' => '',
            'end_date' => $created_at4
                ));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email & password left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/stats', array('email' => '', 'password' => '', 'start_date' => '', 'end_date' => $created_at4));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for returning stats with deleted activities';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/stats', array('email' => 'stats@test.com', 'password' => 'password', 'start_date' => '', 'end_date' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        $this->assertTrue($json->total == 2);
        echo ' . . . Ok' . "\n";

        echo '[#] Test if user has no activities - must return 0';
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/stats', array('email' => 'stats2@test.com', 'password' => 'password', 'start_date' => '', 'end_date' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_activities_returned'));
        $this->assertTrue($json->total == 0);
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test testCreateTeam() function from API Controller
     * Done?
     *  [#] Test for succesful creation of teams
     *  [#] Test for teams in database
     *  [#] Test for creation of teams with existent name
     *  [#] Test for name field left blank
     *  [#] Test for wrong/non-existent email
     *  [#] Test for wrong password
     *  [#] Test for email & password left blank
     * 
     * @return void
     */
    public function testCreateTeam()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Create Teams' . "\n";
        echo '---------------------------' . "\n";
        $client = static::createClient();

        echo '[#] Test for succesful creation of teams';

        $crawler = $client->request('POST', '/api/team/create', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'name' => 'TestCreate'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_team_created'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for teams in database ';

        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $team = $em->getRepository('IBWWebsiteBundle:Team')->findByName('Test');
        $this->assertNotNull($team);
        echo ' . . . Ok' . "\n";

        echo '[#] Test for creation of teams with existent name';

        $crawler = $client->request('POST', '/api/team/create', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'name' => 'TestCreate'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('team_name_taken'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for name field left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/create', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'name' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == "Name required ");
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong/non-existent email ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/create', array('email' => 'test@testing.com', 'password' => 'password', 'name' => 'Test'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong password ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/create', array('email' => 'test@intelligentbee.com', 'password' => 'gibberish', 'name' => 'Test'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email & password left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/create', array('email' => '', 'password' => '', 'name' => 'Test'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test testJoinTeam() function from API Controller
     * Done?
     *  [#] Test for succesful join in a team
     *  [#] Test for joining a team that user si already member of
     *  [#] Test for name field left blank
     *  [#] Test for wrong/non-existent email
     *  [#] Test for wrong password
     *  [#] Test for email & password left blank
     * 
     * @return void
     */
    public function testJoinTeam()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Join team' . "\n";
        echo '---------------------------' . "\n";
        $client = static::createClient();
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $team_repository = $em->getRepository('IBWWebsiteBundle:Team');
        $team = $team_repository->findOneByName('Test');

        echo '[#] Test for succesful join in a team';

        $crawler = $client->request('POST', '/api/team/join', array('email' => 'test@test.com', 'password' => 'password', 'id' => $team->getId()));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_team_joined'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for joining a team that user si already member of';

        $crawler = $client->request('POST', '/api/team/join', array('email' => 'test@test.com', 'password' => 'password', 'id' => $team->getId()));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_team_already_joined'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for id field left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/join', array('email' => 'test@test.com', 'password' => 'password', 'id' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('team_name_not_found'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong/non-existent email ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/join', array('email' => 'test@testds.com', 'password' => 'password', 'id' => $team->getId()));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong password ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/join', array('email' => 'test@test.com', 'password' => 'gibberish', 'id' => $team->getId()));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email & password left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/join', array('email' => '', 'password' => '', 'id' => $team->getId()));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test testgetUserTeams() function from API Controller
     * Done?
     *  [#] Test succesfull return of teams
     *  [#] Test for non existent email/wrong email
     *  [#] Test for wrong password
     *  [#] Test for email & password left blank
     * 
     * @return void
     */
    public function testGetUserTeams()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Get Users teams' . "\n";
        echo '---------------------------' . "\n";
        $client = static::createClient();

        echo '[#] Test for succesful return of teams';

        $crawler = $client->request('POST', '/api/user/teams', array('email' => 'test@intelligentbee.com', 'password' => 'password'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('teams', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_teams_returned'));
        foreach ($json->teams as $team) {
            $this->assertTrue($team->name == 'Test' || $team->name == 'TestCreate');
        }
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong/non-existent email ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/teams', array('email' => 'test@gibbersih.com', 'password' => 'gibberish'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong password ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/teams', array('email' => 'test@intelligentbee.com', 'password' => 'gibberish'));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email & password left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/user/teams', array('email' => '', 'password' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test testGetUserStats() function from API Controller
     * Done?
     *  [#] Test succesfull return of stats without dates
     *  [#] Test succesfull return of stats with dates
     *  [#] Test succesfull return of stats with start_date
     *  [#] Test succesfull return of stats with end date
     *  [#] Test for returning stats with deleted activities
     *  [#] Test if team has no activities - must return 0
     * 
     * @return void
     */
    public function testGetTeamStats()
    {
        $created_at1 = '2012-10-12 05:18:14';
        $created_at2 = '2012-10-13 05:18:14';
        $created_at3 = '2012-10-14 05:18:14';
        $created_at4 = '2012-10-15 05:18:14';
        $created_at5 = '2012-10-16 05:18:14';
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Get Team Stats' . "\n";
        echo '---------------------------' . "\n";
        $client = static::createClient();
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('IBWWebsiteBundle:Team');
        $team = $repository->findOneByName('Test');

        echo '[#] Test succesfull return of stats without dates';

        $crawler = $client->request('POST', '/api/team/stats', array('id' => $team->getId()));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_stats_returned'));
        $this->assertTrue($json->total == 144);
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of stats with dates';

        $crawler = $client->request('POST', '/api/team/stats', array('id' => $team->getId(), 'start_date' => $created_at1, 'end_date' => $created_at3));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_stats_returned'));
        $this->assertTrue($json->total == 12);
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of stats with start_date';

        $crawler = $client->request('POST', '/api/team/stats', array('id' => $team->getId(), 'start_date' => $created_at1, 'end_date' => ''));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_stats_returned'));
        $this->assertTrue($json->total == 144);
        echo ' . . . Ok' . "\n";

        echo '[#] Test succesfull return of stats with end_date';

        $crawler = $client->request('POST', '/api/team/stats', array('id' => $team->getId(), 'start_date' => '', 'end_date' => $created_at3));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_stats_returned'));
        $this->assertTrue($json->total == 12);
        echo ' . . . Ok' . "\n";

        echo '[#] Test for returning stats with deleted activities';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/stats', array('id' => $team->getId(), 'start_date' => '', 'end_date' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_stats_returned'));
        $this->assertTrue($json->total == 144);
        echo ' . . . Ok' . "\n";

        $team = $repository->findOneByName('TestStats');

        echo '[#] Test if user has no activities - must return 0';
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/stats', array('id' => $team->getId(), 'start_date' => $created_at1, 'end_date' => ''));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertObjectHasAttribute('total', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_stats_returned'));
        $this->assertTrue($json->total == 0);
        echo ' . . . Ok' . "\n";
    }

    /**
     * Test testLeaveTeam() function from API Controller
     * Done?
     *  [#] Test for succesful leave
     *  [#] Test for leaving a team that user has already left
     *  [#] Test for id field left blank
     *  [#] Test for wrong team (no team)
     *  [#] Test for wrong existing team
     *  [#] Test for leaving if owner
     *  [#] Test for wrong/non-existent email 
     *  [#] Test for wrong password 
     *  [#] Test for email & password left blank
     * 
     * @return void
     */
    public function testLeaveTeam()
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Test Leave team' . "\n";
        echo '---------------------------' . "\n";
        $client = static::createClient();
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('IBWWebsiteBundle:Team');
        $team = $repository->findOneByName('Test');
        $teamc = $repository->findOneByName('TestCreate');

        echo '[#] Test for succesful leave';

        $crawler = $client->request('POST', '/api/team/leave', array('email' => 'test@test.com', 'password' => 'password', 'id' => $team->getId()));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_success'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_team_left'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for leaving a team that user has already left';

        $crawler = $client->request('POST', '/api/team/leave', array('email' => 'test@test.com', 'password' => 'password', 'id' => $team->getId()));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_team_no_user'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for id field left blank';

        $crawler = $client->request('POST', '/api/team/leave', array('email' => 'test@test.com', 'password' => 'password', 'id' => ''));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('team_name_not_found'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong team (no team)';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/leave', array('email' => 'test@test.com', 'password' => 'password', 'id' => 12234612));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('team_name_not_found'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong existing team';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/leave', array('email' => 'test@test.com', 'password' => 'password', 'id' => $teamc->getId()));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_team_no_user'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for leaving if owner';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/leave', array('email' => 'test@intelligentbee.com', 'password' => 'password', 'id' => $teamc->getId()));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_team_left_owner'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong/non-existent email ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/leave', array('email' => 'test@testsds.com', 'password' => 'password', 'id' => $team->getId()));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for wrong password ';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/leave', array('email' => 'test@test.com', 'password' => 'gibberish', 'id' => $team->getId()));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        echo '[#] Test for email & password left blank';

        $client = static::createClient();
        $crawler = $client->request('POST', '/api/team/leave', array('email' => '', 'password' => '', 'id' => $team->getId()));
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $json = json_decode($client->getResponse()->getContent());
        $this->assertNotNull($json);
        $this->assertObjectHasAttribute('result', $json);
        $this->assertObjectHasAttribute('message', $json);
        $this->assertTrue($json->result == $client->getContainer()->getParameter('result_error'));
        $this->assertTrue($json->message == $client->getContainer()->getParameter('message_wrong_email_or_password'));
        echo ' . . . Ok' . "\n";

        self::databaseClear();
    }

}
