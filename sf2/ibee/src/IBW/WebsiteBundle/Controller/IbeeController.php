<?php

namespace IBW\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 *  IbeeController Class contains Actions for IBee Website
 */
class IbeeController extends Controller
{

    /**
     * Renders index page
     * 
     * @return Response The response contains the content from the template
     */
    public function indexAction()
    {
        return $this->render('IBWWebsiteBundle:Ibee:index.html.twig');
    }

    /**
     * Renders symfony development page
     * 
     * @return Response The response contains the content from the template
     */
    public function sfIndexAction()
    {
        return $this->render('IBWWebsiteBundle:Ibee:symfony.html.twig');
    }

    /**
     * Renders mobile development page
     * 
     * @return Response The response contains the content from the template
     */
    public function mobileIndexAction()
    {
        return $this->render('IBWWebsiteBundle:Ibee:mobile.html.twig');
    }

    /**
     * Renders jobs page
     * 
     * @return Response The response contains the content from the template
     */
    public function jobsIndexAction()
    {
        return $this->render('IBWWebsiteBundle:Ibee:jobs.html.twig');
    }

    /**
     * Renders contact page
     * 
     * @return Response The response contains the content from the template
     */
    public function contactIndexAction()
    {
        return $this->render('IBWWebsiteBundle:Ibee:contact.html.twig');
    }

}

