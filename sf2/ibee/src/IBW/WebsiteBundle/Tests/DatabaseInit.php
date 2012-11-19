<?php

namespace IBW\WebsiteBundle\Tests;

use IBW\WebsiteBundle\Entity\User;
use IBW\WebsiteBundle\Entity\Team;
use IBW\WebsiteBundle\Entity\UserTeam;
use IBW\WebsiteBundle\Entity\GcmDevice;
use IBW\WebsiteBundle\Entity\StairsActivity;

/**
 * Database Initialisation class
 */
class DatabaseInit
{

    /**
     * Clears test database
     */
    public static function databaseClear($kernel)
    {
        echo "\n" . '---------------------------' . "\n";
        echo 'Deleting database' . "\n";
        echo '---------------------------' . "\n";

        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $user_repository = $em->getRepository('IBWWebsiteBundle:User');
        $users = $user_repository->findAll();
        foreach ($users as $user) {
            $em->remove($user);
        }
        $repository = $em->getRepository('IBWWebsiteBundle:Team');
        $teams = $repository->findAll();
        $repository = $em->getRepository('IBWWebsiteBundle:UserTeam');
        $user_teams = $repository->findAll();
        foreach ($teams as $team) {
            $em->remove($team);
        }
        foreach ($user_teams as $user_team) {
            $em->remove($user_team);
        }
        $repository = $em->getRepository('IBWWebsiteBundle:StairsActivity');
        $activities = $repository->findAll();
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
     */
    public static function databasePopulation($kernel)
    {
        self::databaseClear($kernel);
        echo "\n" . '---------------------------' . "\n";
        echo 'Populating database' . "\n";
        echo '---------------------------' . "\n";
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $users_array = array();
        $users = array(
            array('email' => 'teamowner@team.com', 'password' => 'password', 'id' => 1),
            array('email' => 'teammember@team.com', 'password' => 'password', 'id' => 2),
            array('email' => 'teammember2@team.com', 'password' => 'password', 'id' => 3),
            array('email' => 'teststats@team.com', 'password' => 'password', 'id' => 4),
            array('email' => 'testtop@team.com', 'password' => 'password', 'id' => 5),
        );
        $metadata = $em->getClassMetaData('IBW\WebsiteBundle\Entity\User');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        foreach ($users as $usr) {
            $user = new User();
            $user->setId($usr['id']);
            $user->setEmail($usr['email']);
            $factory = $kernel->getContainer()->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($usr['password'], $user->getSalt());
            $user->setPassword($password);
            array_push($users_array, $user);
            $em->persist($user);
        }
        $stair_activities = array(
            array('id' => 1, 
                'user' => $users_array[3],
                'amount' => 120, 'lat' => null,
                'lng' => null,
                'is_notification_sent' => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s')),
            array('id' => 2, 
                'user' => $users_array[3],
                'amount' => 121, 'lat' => null,
                'lng' => null,
                'is_notification_sent' => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 day', time()))),
            array('id' => 3, 
                'user' => $users_array[3],
                'amount' => 122, 'lat' => null,
                'lng' => null,
                'is_notification_sent' => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-14 day', time()))),
            array('id' => 4, 
                'user' => $users_array[3],
                'amount' => 123,
                'lat' => null,
                'lng' => null,
                'is_notification_sent' => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 day', time()))),
            array('id' => 5, 
                'user' => $users_array[3],
                'amount' => 124,
                'lat' => null,
                'lng' => null,
                'is_notification_sent' => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-65 day', time()))),
            array('id' => 6, 
                'user' => $users_array[4],
                'amount' => 110,
                'lat' => null,
                'lng' => null,
                'is_notification_sent' => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s')),
            array('id' => 7, 
                'user' => $users_array[4],
                'amount' => 130,
                'lat' => null,
                'lng' => null,
                'is_notification_sent' => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 day', time()))),
            array('id' => 8, 
                'user' => $users_array[4],
                'amount' => 150,
                'lat' => null,
                'lng' => null,
                'is_notification_sent' => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-14 day', time()))),
            array('id' => 9, 
                'user' => $users_array[4],
                'amount' => 20,
                'lat' => null,
                'lng' => null,
                'is_notification_sent' => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 day', time()))),
            array('id' => 10, 
                'user' => $users_array[4],
                'amount' => 121,
                'lat' => null,
                'lng' => null,
                'is_notification_sent' => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-65 day', time()))),
            array('id' => 11, 
                'user' => $users_array[0],
                'amount' => 121,
                'lat' => 11.1,
                'lng' => 14.5,
                'is_notification_sent' => 1,
                'is_deleted' => 0,
                'created_at' => '2012-01-01 08:00:00'),
        );
        $metadata = $em->getClassMetaData('IBW\WebsiteBundle\Entity\StairsActivity');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        foreach ($stair_activities as $stairs_activity) {
            $stairsActivity = new StairsActivity();
            $stairsActivity->setId($stairs_activity['id']);
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
            array('name' => 'Test', 'owner' => $users_array[0], 'id' => 1),
            array('name' => 'Team1234', 'owner' => $users_array[0], 'id' => 2),
        );
        $metadata = $em->getClassMetaData('IBW\WebsiteBundle\Entity\Team');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata = $em->getClassMetaData('IBW\WebsiteBundle\Entity\UserTeam');
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        foreach ($teams as $team) {
            $tm = new Team();
            $tm->setName($team['name']);
            $tm->setOwner($team['owner']);
            $tm->setId($team['id']);
            $userTeam = new UserTeam();
            $userTeam->setTeam($tm);
            $userTeam->setUser($team['owner']);
            array_push($tms, $tm);
            $em->persist($userTeam);
            $em->persist($tm);
        }
        $user_teams = array(
            array('team' => $tms[0], 'user' => $users_array[1], 'id' => 2),
        );
        $usr_tms = array();

        foreach ($user_teams as $user_team) {
            $userTeam = new UserTeam();
            $userTeam->setTeam($user_team['team']);
            $userTeam->setUser($user_team['user']);
            $userTeam->setId($user_team['id']);
            array_push($usr_tms, $tm);
            $em->persist($userTeam);
        }
        $gcm_devices = array(
            array('user' => $users_array[0], 'reg_id' => 'abcd1234'),
        );
        foreach ($gcm_devices as $gcm_device) {
            $gcmDevice = new GcmDevice();
            $gcmDevice->setUser($gcm_device['user']);
            $gcmDevice->setRegId($gcm_device['reg_id']);
            $em->persist($gcmDevice);
        }
        $em->flush();
    }

}

