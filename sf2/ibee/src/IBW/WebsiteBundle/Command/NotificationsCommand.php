<?php

namespace IBW\WebsiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sends notifications of recent activities to registered Android devices of teammates.
 */
class NotificationsCommand extends ContainerAwareCommand
{

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('ibw:send-notifications')
                ->setDescription('Sends notifications to registered GCM devices using Google Cloud Messaging service');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiKey = $this->getContainer()->getParameter('google_api_key');
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $stairs_activities = $em->getRepository('IBWWebsiteBundle:StairsActivity')->getForNotifications();
        foreach ($stairs_activities as $stairs_activity) {
            $stairs_activity->setIsNotificationSent(true);
            $em->persist($stairs_activity);
            $em->flush();
            $registrationIDs = array();
            // Get the registered devices of users from the same teams as the current activity user
            $activityUser = $stairs_activity->getUser();
            foreach ($activityUser->getTeams() as $team) {
                foreach ($team->getUsers() as $user) {
                    if ($user->getId() != $activityUser->getId()) {
                        foreach ($user->getGcmDevices() as $device) {
                            $registrationIDs[] = $device->getRegId();
                        }
                    }
                }
            }
            $registrationIDs = array_unique($registrationIDs);
            if (count($registrationIDs)) {
                // Build the message to be sent
                $message = $stairs_activity->getUser()->getName() . " climbed " . $stairs_activity->getAmount() . " stairs!";
                $url = $this->getContainer()->getParameter('google_gcm_url');
                // Set POST variables
                $fields = array(
                    'registration_ids' => $registrationIDs,
                    'data'             => array(
                        "id"      => $stairs_activity->getId(),
                        "message" => $message,
                    ),
                );
                $headers = array(
                    'Authorization: key=' . $apiKey,
                    'Content-Type: application/json'
                );
                // Send the notification
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
                curl_close($ch);
                // parse the response
                $json_response = json_decode($result);
                if ($json_response !== null && is_array($json_response->results)) {
                    foreach ($json_response->results as $key => $result) {
                        if (isset($result->registration_id)) {
                            // registration_id has changed for the device
                            $device = $em->getRepository('IBWWebsiteBundle:GcmDevice')
                                         ->findOneBy(array('reg_id' => $registrationIDs[$key]));
                            if ($device) {
                                if ($em->getRepository('IBWWebsiteBundle:GcmDevice')
                                                ->findOneBy(array('reg_id' => $result->registration_id))) {
                                    // We already have a device with the new registration id in the database,
                                    // probably registered before this, so we remove the old one
                                    $em->remove($device);
                                    $em->flush();
                                } else {
                                    // Change the registration_id to the new one
                                    $device->setRegId($result->registration_id);
                                    $em->persist($device);
                                    $em->flush();
                                }
                            }
                        }
                        if (isset($result->error)) {
                            $error_types = array('NotRegistered', 'InvalidRegistration', 'MissingRegistration');
                            if (in_array($result->error, $error_types)) {
                                // We have an error indicating that the registration_id of the device is no longer valid
                                // so we need to delete it
                                $device = $em->getRepository('IBWWebsiteBundle:GcmDevice')
                                        ->findOneBy(array('reg_id' => $registrationIDs[$key]));
                                if ($device) {
                                    $em->remove($device);
                                    $em->flush();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}