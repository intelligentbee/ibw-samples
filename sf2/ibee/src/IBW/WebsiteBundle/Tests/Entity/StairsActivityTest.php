<?php

namespace IBW\WebsiteBundle\Tests\Entity;

use IBW\WebsiteBundle\Tests\DatabaseInit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test for StairsActivty Entity functions
 */
class StairsActivityTest extends WebTestCase
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
     * Test addUserTeam() method for StairsActivty Entity class
     *
     * @return void
     */
    function testGetAmount()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        DatabaseInit::databasePopulation($kernel);
        echo '.Test GetAmount' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $this->assertEquals($activity[0]->getAmount(), 121);
    }
    
    /**
     * Test GetCreatedAt() method for StairsActivty Entity class
     *
     * @return void
     */
    function testGetCreatedAt()
    {
        echo 'Test GetAmount' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $this->assertInstanceOf('\Datetime', $activity[0]->getCreatedAt());
        $this->assertEquals($activity[0]->getCreatedAt(), new \Datetime('2012-01-01 08:00:00'));
    }
    
    /**
     * Test GetId() method for StairsActivty Entity class
     *
     * @return void
     */
    function testGetId()
    {
        echo 'Test GetAmount' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $this->assertEquals($activity[0]->getId(), 11);
    }
    
    /**
     * Test GetId() method for StairsActivty Entity class
     *
     * @return void
     */
    function testGetIsDeleted()
    {
        echo 'Test GetIsDeleted' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $this->assertEquals($activity[0]->getIsDeleted(), false);
    }
    
    /**
     * Test getIsNotificationSent() method for StairsActivty Entity class
     *
     * @return void
     */
    function testGetIsNotificationSent()
    {
        echo 'Test GetIsNotificationSent' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $this->assertEquals($activity[0]->getIsNotificationSent(), true);
    }
    
    /**
     * Test getLat() method for StairsActivty Entity class
     *
     * @return void
     */
    function testGetLat()
    {
        echo 'Test GetLat' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $this->assertEquals($activity[0]->getLat(), 11.1);
    }
    
    /**
     * Test getLng() method for StairsActivty Entity class
     *
     * @return void
     */
    function testGetLng()
    {
        echo 'Test getLng' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $this->assertEquals($activity[0]->getLng(), 14.5);
    }
    
    /**
     * Test getUser() method for StairsActivty Entity class
     *
     * @return void
     */
    function testGetUser()
    {
        echo 'Test getUser' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $this->assertEquals($activity[0]->getUser()->getEmail(), $user->getEmail());
    }
    
    /**
     * Test setAmount() method for StairsActivty Entity class
     *
     * @return void
     */
    function testSetAmount()
    {
        echo 'Test setAmount' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $return = $activity[0]->setAmount(122);
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\StairsActivity', $return);
        $this->assertEquals($activity[0]->getAmount(), 122);
    }
    
    /**
     * Test setCreatedAt() method for StairsActivty Entity class
     *
     * @return void
     */
    function testSetCreatedAt()
    {
        echo 'Test setCreatedAt' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $date = new \Datetime('2011-12-12 09:00:01');
        $return = $activity[0]->setCreatedAt($date);
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\StairsActivity', $return);
        $this->assertEquals($activity[0]->getCreatedAt(), $date);
    }
    
    /**
     * Test setCreatedAtValue() method for StairsActivty Entity class
     *
     * @return void
     */
    function testSetCreatedAtValue()
    {
        echo 'Test setCreatedAtValue' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $date = new \Datetime('2011-12-12 09:00:01');
        $return = $activity[0]->setCreatedAt(null);
        $return = $activity[0]->setCreatedAtValue();
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\StairsActivity', $return);
        $this->assertInstanceOf('\Datetime', $activity[0]->getCreatedAt());
        $this->assertNotNull($activity[0]->getCreatedAt());
        $this->assertNotEquals($activity[0]->getCreatedAt(), $date);
    }
    
    /**
     * Test setIsDeleted() method for StairsActivty Entity class
     *
     * @return void
     */
    function testSetIsDeleted()
    {
        echo 'Test setIsDeleted' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $return = $activity[0]->setIsDeleted(true);
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\StairsActivity', $return);
        $this->assertEquals($activity[0]->getIsDeleted(), true);
    }
    
    /**
     * Test setIsNotificationSent() method for StairsActivty Entity class
     *
     * @return void
     */
    function testSetIsNotificationSent()
    {
        echo 'Test setIsNotificationSent' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $return = $activity[0]->setIsNotificationSent(false);
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\StairsActivity', $return);
        $this->assertEquals($activity[0]->getIsNotificationSent(), false);
    }
    
    /**
     * Test setLat() method for StairsActivty Entity class
     *
     * @return void
     */
    function testSetLat()
    {
        echo 'Test setLat' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $return = $activity[0]->setLat(-122);
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\StairsActivity', $return);
        $this->assertEquals($activity[0]->getLat(), -122);
    }
    
    /**
     * Test setLng() method for StairsActivty Entity class
     *
     * @return void
     */
    function testSetLng()
    {
        echo 'Test setLng' . "\n";
        $user = self::getUser('teamowner@team.com');
        $activity = $user->getStairsActivities();
        $return = $activity[0]->setLng(123);
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\StairsActivity', $return);
        $this->assertEquals($activity[0]->getLng(), 123);
    }
    
    /**
     * Test setUser() method for StairsActivty Entity class
     *
     * @return void
     */
    function testSetUser()
    {
        echo 'Test setUser' . "\n";
        $user = self::getUser('teamowner@team.com');
        $new_user = self::getUser('teammember@team.com');
        $activity = $user->getStairsActivities();
        $return = $activity[0]->setUser($new_user);
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\StairsActivity', $return);
        $this->assertEquals($activity[0]->getUser(), $new_user);
    }
    
    /**
     * Test setId() method for StairsActivty Entity class
     *
     * @return void
     */
    function testSetId()
    {
        echo 'Test setId' . "\n";
        $user = self::getUser('teamowner@team.com');
        $new_user = self::getUser('teammember@team.com');
        $activity = $user->getStairsActivities();
        $return = $activity[0]->setId(345);
        $this->assertInstanceOf('IBW\WebsiteBundle\Entity\StairsActivity', $return);
        $this->assertEquals($activity[0]->getId(), 345);
    }
}

