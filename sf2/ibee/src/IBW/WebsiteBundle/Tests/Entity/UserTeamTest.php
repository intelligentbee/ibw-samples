<?php

namespace IBW\WebsiteBundle\Tests\Entity;

use IBW\WebsiteBundle\Tests\DatabaseInit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test for UserTeam Entity functions
 */
class UserTeamTest extends WebTestCase
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
     * Test getId() method for UserTeam Entity class
     *
     * @return void
     */
    function testGetId()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        DatabaseInit::databasePopulation($kernel);
        echo 'Test GetId' . "\n";
        $team = self::getTeam('Test');
        $user_teams = $team->getUserTeams();
        $this->assertEquals($user_teams[0]->getId(), 2);
    }
    
    /**
     * Test GetTeam() method for UserTeam Entity class
     *
     * @return void
     */
    function testGetTeam()
    {
        echo 'Test GetTeam' . "\n";
        $team = self::getTeam('Test');
        $user_teams = $team->getUserTeams();
        $this->assertEquals($user_teams[0]->getTeam()->getName(), 'Test');
    }
    
    /**
     * Test GetUser() method for UserTeam Entity class
     *
     * @return void
     */
    function testGetUser()
    {
        echo 'Test GetUser' . "\n";
        $team = self::getTeam('Test');
        $user_teams = $team->getUserTeams();
        $this->assertEquals($user_teams[0]->getUser()->getEmail(), 'teammember@team.com');
    }
    
    /**
     * Test SetTeam() method for UserTeam Entity class
     *
     * @return void
     */
    function testSetTeam()
    {
        echo 'Test SetTeam' . "\n";
        $team = self::getTeam('Test');
        $user_teams = $team->getUserTeams();
        $team2 = self::getTeam('Team1234');
        foreach($user_teams as $user_team) {
            $return = $user_team->setTeam($team2);
            $this->assertInstanceOf('IBW\WebsiteBundle\Entity\UserTeam', $return);
            $this->assertEquals($user_team->getTeam()->getName(), 'Team1234');
        }
    }
    /**
     *  Test SetUser() method for UserTeam Entity class
     *
     * @return void
     */
    function testSetUser()
    {
        echo 'Test SetUser' . "\n";
        $team = self::getTeam('Test');
        $user = self::getUser('teammember2@team.com');
        $user_teams = $team->getUserTeams();
        $return = $user_teams[0]->setUser($user);
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\UserTeam', $return);
        $this->assertEquals($user_teams[0]->getUser()->getEmail(), 'teammember2@team.com');
    }
    /**
     * Test SetId() method for UserTeam Entity class
     *
     * @return void
     */
    function testSetId()
    {
        echo 'Test SetId' . "\n";
        $team = self::getTeam('Test');
        $user_teams = $team->getUserTeams();
        $return = $user_teams[0]->setId(5423123);
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\UserTeam', $return);
        $this->assertEquals($user_teams[0]->getId(), 5423123);
    }
}

