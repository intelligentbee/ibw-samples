<?php

namespace IBW\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use IBW\WebsiteBundle\Entity\GcmDevice;

class GCMController extends Controller
{

    /**
     * Registers a new device for a specific user (or changes an existing device user)
     * 
     * @return Response Empty response
     */
    public function registerAction()
    {
        $request = $this->getRequest();
        $regId = $request->request->get('regId', $request->query->get('regId'));
        $email = $request->request->get('email', $request->query->get('email'));
        if ($regId && $email) {
            $device = $this->getDoctrine()->getRepository('IBWWebsiteBundle:GcmDevice')->findOneBy(array('reg_id' => $regId));
            $user = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User')->findOneBy(array('email' => $email));
            if ($user) {
                $em = $this->getDoctrine()->getEntityManager();
                if ($device) {
                    $device->setUser($user);
                } else {
                    $device = new GcmDevice();
                    $device->setRegId($regId);
                    $device->setUser($user);
                }
                $em->persist($device);
                $em->flush();
            }
        }

        return new Response();
    }

    /**
     * Deletes a previously registered device from the database
     * 
     * @return Response Empty response
     */
    public function unregisterAction()
    {
        $request = $this->getRequest();
        $regId = $request->request->get('regId', $request->query->get('regId'));
        if ($regId) {
            if ($device = $this->getDoctrine()->getRepository('IBWWebsiteBundle:GcmDevice')->findOneBy(array('reg_id' => $regId))) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($device);
                $em->flush();
            }
        }

        return new Response();
    }

}
