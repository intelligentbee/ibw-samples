<?php

namespace IBW\WebsiteBundle\Tests\Entity;

use IBW\WebsiteBundle\Entity\UserTeam;
use IBW\WebsiteBundle\Tests\DatabaseInit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test for Team Entity functions
 */
class TeamTest extends WebTestCase
{

    /**
     * Helper function for getting a user from repository by email or by id
     *
     * @param string    $email  Optional Email of user
     * @param integer   $id     Optional Id of user
     * @return IBW\WebsiteBundle\Entity\User User object or null if no parameters provided
     */
    public static function getUser($email = null, $id = null)
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $user_repository = $em->getRepository('IBWWebsiteBundle:User');
        if ($email) {
            return $user_repository->findOneByEmail($email);
        } elseif ($id) {
            return $user_repository->findOneById($id);
        } else {
            return null;
        }
    }

    /**
     * Helper function for getting a team from repository by name or by id
     *
     * @param string    $name   Optional Name of team
     * @param integer   $id     Optional Id of team
     * @return IBW\WebsiteBundle\Entity\Team    Team object or null if no parameters provided
     */
    public static function getTeam($name = null, $id = null)
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $team_repository = $em->getRepository('IBWWebsiteBundle:Team');
        if ($name) {
            return $team_repository->findOneByName($name);
        } elseif ($id) {
            return $team_repository->findOneById($id);
        } else {
            return null;
        }
    }

    /**
     * Test addUserTeam() method for Team Entity class
     * 
     * @return void
     */
    public function testAddUserTeam()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        DatabaseInit::databasePopulation($kernel);
        echo 'Test AddUserTeam' . "\n";
        $team = self::getTeam('Test');
        $user = self::getUser('teammember@team.com');
        $userTeam = new UserTeam();
        $userTeam->setTeam($team);
        $userTeam->setUser($user);
        $response = $team->addUserTeam($userTeam);
        $userTeams = $team->getUserTeams();
        $this->assertContains($userTeam, $userTeams);
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\Team', $response);
    }

    /**
     * Test getId() method for Team Entity class
     * 
     * @return void
     */
    public function testGetId()
    {
        echo 'Test GetId' . "\n";
        $team = self::getTeam('Test');
        $this->assertEquals($team->getId(), 1);
    }

    /**
     * Test getName() method for Team Entity class
     * 
     * @return void
     */
    public function testGetName()
    {
        echo 'Test GetName' . "\n";
        $team = self::getTeam(null, 1);
        $this->assertEquals($team->getName(), 'Test');
    }

    /**
     * Test getOwner() method for Team Entity class
     * 
     * @return void
     */
    public function testGetOwner()
    {
        echo 'Test GetOwner' . "\n";
        $team = self::getTeam('Test');
        $this->assertEquals($team->getOwner(), 'teamowner@team.com');
    }

    /**
     * Test getUserTeam method for Team Entity class
     * 
     * @return void
     */
    public function testGetUserTeams()
    {
        echo 'Test GetUserTeams' . "\n";
        $team = self::getTeam('Test');
        $userTeams = $team->getUserTeams();
        foreach ($userTeams as $userTeam) {
            $this->assertInstanceOf('IBW\WebsiteBundle\Entity\UserTeam', $userTeam);
        }
        $this->assertEquals(2, $userTeams->count());
    }

    /**
     * Test getUsers method for Team Entity class
     * 
     * @return void
     */
    public function testGetUsers()
    {
        echo 'Test GetUsers' . "\n";
        $team = self::getTeam('Test');
        $users = $team->getUsers();
        foreach ($users as $user) {
            $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $user);
        }
        $this->assertEquals(2, count($users));
    }

    /**
     * Test hasUser method for Team Entity class
     * 
     * @return void
     */
    public function testHasUser()
    {
        echo 'Test HasUser' . "\n";
        $team = self::getTeam('Test');
        $user = self::getUser('teammember@team.com');
        $user2 = self::getUser('teammember2@team.com');
        $this->assertTrue($team->hasUser($user));
        $this->assertFalse($team->hasUser($user2));
    }

    /**
     * Test removeUserTeam method for Team Entity class
     * 
     * @return void
     */
    public function testRemoveUserTeam()
    {
        echo 'Test RemoveUserTeam' . "\n";
        $team = self::getTeam('Test');
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $user_team_repository = $em->getRepository('IBWWebsiteBundle:UserTeam');
        $user = self::getUser('teammember@team.com');
        $userTeam = $user_team_repository->findOneByUser($user);
        $team->removeUserTeam($userTeam);
        $this->assertNotContains($userTeam, $team->getUserTeams());
    }

    /**
     * Test setId method for Team Entity class
     * 
     * @return void
     */
    public function testSetId()
    {
        echo 'Test SetId' . "\n";
        $team = self::getTeam('Test');
        $team->setId(9);
        $this->assertEquals(9, $team->getId());
    }

    /**
     * Test setName method for Team Entity class
     * 
     * @return void
     */
    public function testSetName()
    {
        echo 'Test SetName' . "\n";
        $team = self::getTeam('Test');
        $team->setName('Team1234');
        $this->assertEquals('Team1234', $team->getName());
    }

    /**
     * Test setName method for Team Entity class
     * 
     * @return void
     */
    public function testSetOwner()
    {
        echo 'Test SetOwner' . "\n";
        $team = self::getTeam('Test');
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $user = self::getUser('teammember@team.com');
        $team->setOwner($user);
        $this->assertEquals($user, $team->getOwner());
        DatabaseInit::databaseClear($kernel);
    }

}

