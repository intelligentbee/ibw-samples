<?php

namespace IBW\WebsiteBundle\Tests\Entity;

use IBW\WebsiteBundle\Entity\Team;
use IBW\WebsiteBundle\Entity\GcmDevice;
use IBW\WebsiteBundle\Entity\UserTeam;
use IBW\WebsiteBundle\Entity\StairsActivity;
use IBW\WebsiteBundle\Tests\DatabaseInit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test for User Entity functions
 */
class UserTest extends WebTestCase
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
     * Test __sleep() method for User Entity class
     * 
     * @return void
     */
    public function test__sleep()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        DatabaseInit::databasePopulation($kernel);
        echo '.Test __sleep' . "\n";
        $user = self::getUser('teamowner@team.com');
        $this->assertContains('id', $user->__sleep());
    }

    /**
     * Test __toString() method for User Entity class
     * 
     * @return void
     */
    public function test__toString()
    {
        echo 'Test __toString' . "\n";
        $user = self::getUser('teamowner@team.com');
        $this->assertEquals($user->getEmail(), $user->__toString());
    }

    /**
     * Test addGcmDevice() method for User Entity class
     * 
     * @return void
     */
    public function testAddGcmDevice()
    {
        echo 'Test AddGcmDevice' . "\n";
        $user = self::getUser('teamowner@team.com');
        $gcmDevices = new GcmDevice();
        $gcmDevices->setRegId('qwerty');
        $return = $user->addGcmDevice($gcmDevices);
        $this->assertContains($gcmDevices, $user->getGcmDevices());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
    }

    /**
     * Test addOwnedTeam() method for User Entity class
     * 
     * @return void
     */
    public function testAddOwnedTeam()
    {
        echo 'Test AddOwnedTeam' . "\n";
        $user = self::getUser('teamowner@team.com');
        $team = new Team();
        $team->setName('Tester');
        $team->setOwner($user);
        $return = $user->addOwnedTeam($team);
        $this->assertContains($team, $user->getOwnedTeams());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
    }

    /**
     * Test addStairsActivitie() method for User Entity class
     * 
     * @return void
     */
    public function testAddStairsActivitie()
    {
        echo 'Test AddStairsActivitie' . "\n";
        $user = self::getUser('teamowner@team.com');
        $stairsActivity = new StairsActivity();
        $stairsActivity->setAmount(33);
        $return = $user->addStairsActivitie($stairsActivity);
        $this->assertContains($stairsActivity, $user->getStairsActivities());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
    }

    /**
     * Test addUserTeam() method for User Entity class
     * 
     * @return void
     */
    public function testAddUserTeam()
    {
        echo 'Test AddUserTeam' . "\n";
        $user = self::getUser('teammember2@team.com');
        $team = self::getTeam('Test');
        $userTeam = new UserTeam();
        $userTeam->setTeam($team);
        $userTeam->setUser($user);
        $return = $user->addUserTeam($userTeam);
        $this->assertContains($userTeam, $user->getUserTeams());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
    }

    /**
     * Test getCreatedAt() method for User Entity class
     * 
     * @return void
     */
    public function testGetCreatedAt()
    {
        echo 'Test GetCreatedAt' . "\n";
        $user = self::getUser('teamowner@team.com');
        $this->assertInstanceOf('\Datetime', $user->getCreatedAt());
    }

    /**
     * Test getEmail() method for User Entity class
     */
    public function testGetEmail()
    {
        echo 'Test GetEmail' . "\n";
        $user = self::getUser('teamowner@team.com');
        $this->assertEquals('teamowner@team.com', $user->getEmail());
    }

    /**
     * Test getGcmDevices() method for User Entity class
     * 
     * @return void
     */
    public function testGetGcmDevices()
    {
        echo 'Test GetGcmDevices' . "\n";
        $user = self::getUser('teamowner@team.com');
        $gcmDevices = $user->getGcmDevices();
        foreach ($gcmDevices as $gcmDevice) {
            $this->assertInstanceOf('IBW\WebsiteBundle\Entity\GcmDevice', $gcmDevice);
        }
        $this->assertEquals(1, $gcmDevices->count());
    }

    /**
     * Test getId() method for User Entity class
     * 
     * @return void
     */
    public function testGetId()
    {
        echo 'Test GetId' . "\n";
        $user = self::getUser('teamowner@team.com');
        $this->assertEquals(1, $user->getId());
    }

    /**
     * Test getName() method for User Entity class
     * 
     * @return void
     */
    public function testGetName()
    {
        echo 'Test GetName' . "\n";
        $user = self::getUser('teamowner@team.com');
        $this->assertEquals('teamowner', $user->getName());
    }

    /**
     * Test getOwnedTeams() method for User Entity class
     * 
     * @return void
     */
    public function testGetOwnedTeams()
    {
        echo 'Test GetOwnedTeams' . "\n";
        $user = self::getUser('teamowner@team.com');
        foreach ($user->getOwnedTeams() as $ownedTeam) {
            $this->assertInstanceOf('IBW\WebsiteBundle\Entity\Team', $ownedTeam);
        }
        $this->assertEquals(2, $user->getOwnedTeams()->count());
    }

    /**
     * Test getPassword() method for User Entity class
     * 
     * @return void
     */
    public function testGetPassword()
    {
        echo 'Test GetPassword' . "\n";
        $user = self::getUser('teamowner@team.com');
        $kernel = static::createKernel();
        $kernel->boot();
        $factory = $kernel->getContainer()->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $new_password = $encoder->encodePassword('password', $user->getSalt());
        $this->assertEquals($user->getPassword(), $new_password);
    }

    /**
     * Test getRoles() method for User Entity class
     * 
     * @return void
     */
    public function testGetRoles()
    {
        echo 'Test GetRoles' . "\n";
        $user = self::getUser('teamowner@team.com');
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    /**
     * Test getSalt() method for User Entity class
     * 
     * @return void
     */
    public function testGetSalt()
    {
        echo 'Test GetSalt' . "\n";
        $user = self::getUser('teamowner@team.com');
        $this->assertInternalType('null', $user->getSalt());
    }

    /**
     * Test getStairsActivities() method for User Entity class
     * 
     * @return void
     */
    public function testGetStairsActivities()
    {
        echo 'Test GetStairsActivities' . "\n";
        $user = self::getUser('teststats@team.com');
        foreach ($user->getStairsActivities() as $stairsActivity) {
            $this->assertInstanceOf('IBW\WebsiteBundle\Entity\StairsActivity', $stairsActivity);
        }
        $this->assertEquals(5, $user->getStairsActivities()->count());
    }

    /**
     * Test getTeams() method for User Entity class
     * 
     * @return void
     */
    public function testGetTeams()
    {
        echo 'Test GetTeams' . "\n";
        $user = self::getUser('teamowner@team.com');
        foreach ($user->getTeams() as $team) {
            $this->assertInstanceOf('IBW\WebsiteBundle\Entity\Team', $team);
        }
        $this->assertEquals(2, $user->getTeams()->count());
    }

    /**
     * Test getUpdatedAt() method for User Entity class
     * 
     * @return void
     */
    public function testGetUpdatedAt()
    {
        echo 'Test GetUpdatedAt' . "\n";
        $user = self::getUser('teamowner@team.com');
        $this->assertInstanceOf('\Datetime', $user->getUpdatedAt());
    }

    /**
     * Test getUserTeams() method for User Entity class
     * 
     * @return void
     */
    public function testGetUserTeams()
    {
        echo 'Test GetUserTeams' . "\n";
        $user = self::getUser('teamowner@team.com');
        $userTeams = $user->getUserTeams();
        foreach ($userTeams as $userTeam) {
            $this->assertInstanceOf('IBW\WebsiteBundle\Entity\UserTeam', $userTeam);
        }
        $this->assertEquals(2, $userTeams->count());
    }

    /**
     * Test getUsername() method for User Entity class
     * 
     * @return void
     */
    public function testGetUsername()
    {
        echo 'Test GetUsername' . "\n";
        $user = self::getUser('teamowner@team.com');
        $this->assertEquals('teamowner@team.com', $user->getUsername());
    }

    /**
     * Test getUsername() method for User Entity class
     * 
     * @return void
     */
    public function testRemoveGcmDevice()
    {
        echo 'Test RemoveGcmDevice' . "\n";
        $user = self::getUser('teamowner@team.com');
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $gcm_repository = $em->getRepository('IBWWebsiteBundle:GcmDevice');
        $gcm = $gcm_repository->findAll();
        $user->removeGcmDevice($gcm[0]);
        $this->assertNotContains($gcm[0], $user->getGcmDevices());
    }

    /**
     * Test removeOwnedTeam() method for User Entity class
     * 
     * @return void
     */
    public function testRemoveOwnedTeam()
    {
        echo 'Test RemoveOwnedTeam' . "\n";
        $user = self::getUser('teamowner@team.com');
        $team = self::getTeam('Team1234');
        $user->removeOwnedTeam($team);
        $this->assertNotContains($team, $user->getOwnedTeams());
    }

    /**
     * Test removeStairsActivitie() method for User Entity class
     * 
     * @return void
     */
    public function testRemoveStairsActivitie()
    {
        echo 'Test RemoveStairsActivitie' . "\n";
        $user = self::getUser('teamowner@team.com');
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('IBWWebsiteBundle:StairsActivity');
        $stairsActivity = $repository->findOneByUser($user);
        $user->removeStairsActivitie($stairsActivity);
        $this->assertNotContains($stairsActivity, $user->getStairsActivities());
    }

    /**
     * Test removeUserTeam() method for User Entity class
     * 
     * @return void
     */
    public function testRemoveUserTeam()
    {
        echo 'Test RemoveUserTeam' . "\n";
        $user = self::getUser('teamowner@team.com');
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('IBWWebsiteBundle:UserTeam');
        $userTeam = $repository->findOneByUser($user);
        $user->removeUserTeam($userTeam);
        $this->assertNotContains($userTeam, $user->getUserTeams());
    }

    /**
     * Test setCreatedAt() method for User Entity class
     * 
     * @return void
     */
    public function testSetCreatedAt()
    {
        echo 'Test SetCreatedAt' . "\n";
        $user = self::getUser('teamowner@team.com');
        $dateTime = new \DateTime('2012-12-12 08:00:00');
        $return = $user->setCreatedAt($dateTime);
        $this->assertEquals($dateTime, $user->getCreatedAt());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
    }

    /**
     * Test setCreatedAtValue() method for User Entity class
     * 
     * @return void
     */
    public function testSetCreatedAtValue()
    {
        echo 'Test SetCreatedAtValue' . "\n";
        $user = self::getUser('teamowner@team.com');
        $dateTime = new \DateTime('2012-12-12 09:00:00');
        $return = $user->setCreatedAtValue();
        $this->assertNotEquals($dateTime, $user->getCreatedAt());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
        $return = $user->setCreatedAt($dateTime);
        $return = $user->setCreatedAtValue();
        $this->assertEquals($dateTime, $user->getCreatedAt());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
    }

    /**
     * Test setEmail() method for User Entity class
     * 
     * @return void
     */
    public function testSetEmail()
    {
        echo 'Test SetEmail' . "\n";
        $user = self::getUser('teamowner@team.com');
        $return = $user->setEmail('owner@team.com');
        $this->assertEquals('owner@team.com', $user->getEmail());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
    }

    /**
     * Test setId() method for User Entity class
     * 
     * @return void
     */
    public function testSetId()
    {
        echo 'Test SetId' . "\n";
        $user = self::getUser('teamowner@team.com');
        $return = $user->setId(3718926431);
        $this->assertEquals(3718926431, $user->getId());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
    }

    /**
     * Test setPassword() method for User Entity class
     * 
     * @return void
     */
    public function testSetPassword()
    {
        echo 'Test SetPassword' . "\n";
        $user = self::getUser('teamowner@team.com');
        $return = $user->setPassword('fusdhfh271qwaASDA23!_dea');
        $this->assertEquals('fusdhfh271qwaASDA23!_dea', $user->getPassword());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
    }

    /**
     * Test setUpdatedAt() method for User Entity class
     * 
     * @return void
     */
    public function testSetUpdatedAt()
    {
        echo 'Test GetSetUpdatedAt' . "\n";
        $user = self::getUser('teamowner@team.com');
        $dateTime = new \DateTime('2012-12-12 09:00:00');
        $return = $user->setUpdatedAt($dateTime);
        $this->assertEquals($dateTime, $user->getUpdatedAt());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
    }

    /**
     * Test setCreatedAtValue() method for User Entity class
     * 
     * @return void
     */
    public function testSetUpdatedAtValue()
    {
        echo 'Test SetUpdatedAtValue' . "\n";
        $user = self::getUser('teamowner@team.com');
        $return = $user->setUpdatedAtValue();
        $this->assertInstanceOf('\DateTime', $user->getUpdatedAt());
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\User', $return);
        $kernel = static::createKernel();
        $kernel->boot();
        DatabaseInit::databaseClear($kernel);
    }

}
