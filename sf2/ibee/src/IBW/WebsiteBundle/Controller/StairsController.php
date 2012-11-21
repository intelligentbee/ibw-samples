<?php

namespace IBW\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use IBW\WebsiteBundle\Entity\StairsActivity;
use IBW\WebsiteBundle\Entity\UserTeam;
use IBW\WebsiteBundle\Entity\Team;
use IBW\WebsiteBundle\Entity\User;
use IBW\WebsiteBundle\Form\StairsActivityAmountType;
use IBW\WebsiteBundle\Form\StairsActivityLocationType;
use IBW\WebsiteBundle\Form\StairsActivityAddType;
use IBW\WebsiteBundle\Form\TeamNameType;
use IBW\WebsiteBundle\Form\UserType;

/**
 *  StairsController Class contains Actions that assure functionality of the Stairs website
 */
class StairsController extends Controller
{

    /**
     * Adds a new activity
     * 
     * @return Response The response contains the content from the template
     */
    public function activitiesAddAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $stairActivity = new StairsActivity();
            $user = $this->get('security.context')->getToken()->getUser();
            $stairActivity->setUser($user);
            $add_form = $this->createForm(new StairsActivityAddType(), $stairActivity);
            $add_form->bindRequest($request);
            if ($add_form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($stairActivity);
                $em->flush();
                $message = $this->container->getParameter('message_activity_created');
                $this->get('session')->getFlashBag()->add('messages', $message);

                return $this->redirect($this->generateUrl('ibw_website_activities_page'));
            }
            $stairsActivities = $this->getDoctrine()->getRepository('IBWWebsiteBundle:StairsActivity')
                                     ->findByUser($user, null);
            $this->get('session')->getFlashBag()->add('errors', "Something went wrong");
            
            return $this->render('IBWWebsiteBundle:Stairs:activities.html.twig',
                       array('add_form'   => $add_form->createView(),
                             'activities' => $stairsActivities));
        }
    }

    /**
     * Renders activities page
     * 
     * @return Response The response contains the content from the template
     */
    public function activitiesIndexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $stairActivity = new StairsActivity();
        $stairActivity->setUser($user);
        $stairsActivities = $this->getDoctrine()->getRepository('IBWWebsiteBundle:StairsActivity')->findByUser($user, null);
        $add_form = $this->createForm(new StairsActivityAddType(), $stairActivity);

        return $this->render('IBWWebsiteBundle:Stairs:activities.html.twig', 
                   array('add_form'   => $add_form->createView(),
                         'activities' => $stairsActivities ));
    }

    /**
     * Render a edit page dor every activity
     * 
     * @param integer $id Id of activity
     * @throws NotFoundException If activity not found or is deleted
     * @return Response The response contains the content from the template
     */
    public function activityAction($id)
    {
        $stairActivity = $this->getDoctrine()->getRepository('IBWWebsiteBundle:StairsActivity')->findOneById($id);
        if ($stairActivity && !$stairActivity->getIsDeleted()) {
            $add_form = $this->createForm(new StairsActivityAddType(), $stairActivity);

            return $this->render('IBWWebsiteBundle:Stairs:activity.html.twig',
                       array('add_form' => $add_form->createView(),
                             'activity'    => $stairActivity));
        }
        throw $this->createNotFoundException('The activity does not exist');
    }

    /**
     * Deletes an activity
     * 
     * @param integer $id Id of activity to be deleted
     * @throws NotFoundException If activity not found, is deleted or doesnt belong to user
     * @return Response The response contains the content from the template
     */
    public function activityDelAction($id)
    {
        $stairActivity = $this->getDoctrine()->getRepository('IBWWebsiteBundle:StairsActivity')->findOneById($id);
        if ($stairActivity && !$stairActivity->getIsDeleted()) {
            if ($stairActivity->getUser()->getId() == $this->get('security.context')->getToken()->getUser()->getId()) {
                $stairActivity->setIsDeleted(true);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $message = $this->container->getParameter('message_activity_deletion');
                $this->get('session')->getFlashBag()->add('messages', $message);

                return $this->redirect($this->generateUrl('ibw_website_activities_page'));
            }
        }
        throw $this->createNotFoundException('The activity does not exist');
    }

    /**
     * Edit amount of an activity
     * 
     * @param integer $id Id of activity to be edited
     * @throws NotFoundException If activity not found, is deleted or doesnt belong to user
     * @return Response The response contains the content from the template
     */
    public function activityEditAction($id)
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $stairActivity = $this->getDoctrine()->getRepository('IBWWebsiteBundle:StairsActivity')->findOneById($id);
            if ($stairActivity && !$stairActivity->getIsDeleted()) {
                if ($stairActivity->getUser()->getId() == $this->get('security.context')->getToken()->getUser()->getId()) {
                    $stairActivityUnEdited = clone $stairActivity;
                    $add_form = $this->createForm(new StairsActivityAddType(), $stairActivity);
                    $add_form->bindRequest($request);
                    if ($add_form->isValid()) {
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        $message = $this->container->getParameter('message_activity_updated');
                        $this->get('session')->getFlashBag()->add('messages', $message);

                        return $this->redirect($this->generateUrl('ibw_website_activity_page', array("id" => $stairActivity->getId())));
                    } else {
                        $this->get('session')->getFlashBag()->add('errors', "Something went wrong");

                        return $this->render('IBWWebsiteBundle:Stairs:activity.html.twig', 
                                   array('add_form' => $add_form->createView(),
                                         'activity'    => $stairActivityUnEdited));
                    }
                }
            }
        }
        throw $this->createNotFoundException('The activity does not exist');
    }

    /**
     * Edits the location of an activity
     * 
     * @param integer $id Id of the activity
     * @throws NotFoundException If activity not found, is deleted or doesnt belong to user
     * @return Response The response contains the content from the template
     */
    public function activityEditLocAction($id)
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $stairActivity = $this->getDoctrine()->getRepository('IBWWebsiteBundle:StairsActivity')->findOneById($id);
            $stairActivityUnEdited = clone $stairActivity;
            if ($stairActivity && !$stairActivity->getIsDeleted()) {
                if ($stairActivity->getUser()->getId() == $this->get('security.context')->getToken()->getUser()->getId()) {
                    $amount_form = $this->createForm(new StairsActivityAmountType(), $stairActivity);
                    $loc_form = $this->createForm(new StairsActivityLocationType(), $stairActivity);
                    $loc_form->bindRequest($request);
                    if ($loc_form->isValid()) {
                        $em->flush();
                        $message = $this->container->getParameter('message_activity_updated');
                        $this->get('session')->getFlashBag()->add('messages', $message);

                        return $this->redirect($this->generateUrl('ibw_website_activity_page', array("id" => $stairActivity->getId())));
                    } else {
                        $this->get('session')->getFlashBag()->add('errors', "Something went wrong");
                        return $this->render('IBWWebsiteBundle:Stairs:activity.html.twig', 
                                   array('amount_form' => $amount_form->createView(),
                                         'loc_form'    => $loc_form->createView(),
                                         'activity'    => $stairActivityUnEdited));
                    }
                }
            }
        }
        throw $this->createNotFoundException('The activity does not exist');
    }

    /**
     * Renders a index page
     * 
     * @return Response The response contains the content from the template
     */
    public function indexAction()
    {
        return $this->render('IBWWebsiteBundle:Stairs:index.html.twig');
    }

    /**
     * Renders a login page
     * 
     * @return Response The response contains the content from the template
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        // get the login error if  there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        if ($error) {
            $this->get('session')->getFlashBag()->add('errors', $error->getMessage());
        }

        return $this->render('IBWWebsiteBundle:Stairs:login.html.twig',
                   array('last_username' => $session->get(SecurityContext::LAST_USERNAME)));
    }

    /**
     * Renders a registration page
     * 
     * @return Response The response contains the content from the template
     */
    public function registerAction()
    {
        $request = $this->getRequest();
        $user = new User();
        $register_form = $this->createForm(new UserType(), $user);
        if($request->getMethod() == "POST") {
            $register_form->bindRequest($request);
            if ($register_form->isValid()) {
                $password = $user->getPassword();
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $this->get('session')->getFlashBag()->add('messages', "User Created!");

                return $this->redirect($this->generateUrl('ibw_website_login'));
            } else {
                $this->get('session')->getFlashBag()->add('errors', 'Something went wrong');
            }
        }
        
        return $this->render('IBWWebsiteBundle:Stairs:register.html.twig',
                           array('register_form' => $register_form->createView()));
    }

    /**
     * Renders a user stats page
     * 
     * @return Response The response contains the content from the template
     */
    public function statsIndexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $stats = new \stdClass();
        $stats->total = $repository->getAmountForUser($user, 0, 0);
        if (date('D') != 'Sun') {
            $stats->this_week = $repository->getAmountForUser($user, 
                    date('Y-m-d 00:01:00', strtotime('Monday this week', time())));
        } else {
            $stats->this_week = $repository->getAmountForUser($user, 
                    date('Y-m-d 00:01:00', strtotime('Monday last week', time())));
        }
        $stats->this_month = $repository->getAmountForUser($user, date('Y-m-01 00:01:00'), 0);
        if (date('D') != 'Sun') {
            $stats->last_week = $repository->getAmountForUser($user,
                    date('Y-m-d 00:01:00', strtotime('Monday last week', time())),
                    date('Y-m-d 23:59:00', strtotime('Last Sunday', time())));
        } else {
            $stats->last_week = $repository->getAmountForUser($user,
                    date('Y-m-d 00:01:00', strtotime('Monday -2 week', time())),
                    date('Y-m-d 23:59:00', strtotime('Sunday -1 week', time())));
        }
        $stats->last_month = $repository->getAmountForUser($user,
                date('Y-m-d 00:01:00', strtotime('First day of last month', time())),
                date('Y-m-t 23:59:00', strtotime('last month', time())));
        $activities = $repository->statsForUser($user);

        return $this->render('IBWWebsiteBundle:Stairs:stats.html.twig',
                   array('activities' => $activities,'stats' => $stats));
    }

    /**
     * Creates a new team
     * 
     * @return Response The response contains the content from the template
     */
    public function teamCreateAction()
    {
        $request = $this->getRequest();
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $team = new Team();
        $name_form = $this->createForm(new TeamNameType(), $team);
        $name_form->bindRequest($request);
        $team_found = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team')->findOneByName($team->getName());
        if ($team_found === null) {
            $user_team = new UserTeam();
            $team->setOwner($user);
            $user_team->setTeam($team);
            $user_team->setUser($user);
            $validator = $this->get('validator');
            $errors = $validator->validate($team);
            if (count($errors) == 0) {
                $em->persist($team);
                $em->persist($user_team);
                $em->flush();
                $message = $this->container->getParameter('message_team_created');
                $this->get('session')->getFlashBag()->add('messages', $message);
            } else {
                foreach ($errors as $error) {
                    $this->get('session')->getFlashBag()->add('errors', $error->getMessage());
                }
            }
        } else {
            $error = $this->container->getParameter('team_name_taken');
            $this->get('session')->getFlashBag()->add('errors', $error);
        }
        return $this->redirect($this->generateUrl('ibw_website_teams_page'));
    }

    /**
     * Deletes a team
     * 
     * @param integer $id Id of the team
     * @throws NotFoundException If team not found, is deleted or doesnt belong to user
     * @return Response The response contains the content from the template
     */
    public function teamDeleteAction($id)
    {
        $team = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team')->findOneById($id);
        $user_teams = $this->getDoctrine()->getRepository('IBWWebsiteBundle:UserTeam')->findByTeam($team);
        if ($team && $team->getOwner()->getId() == $this->get('security.context')->getToken()->getUser()->getId()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($user_teams as $user_team) {
                $em->remove($user_team);
            }
            $em->remove($team);
            $em->flush();
            $message = 'Team deleted';
            $this->get('session')->getFlashBag()->add('messages', $message);

            return $this->redirect($this->generateUrl('ibw_website_teams_page'));
        }
        throw $this->createNotFoundException('The team not found');
    }

    /**
     * Renders a team general page
     * 
     * @return Response The response contains the content from the template
     */
    public function teamIndexAction()
    {
        $request = $this->getRequest();
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $teams = $em->getRepository('IBWWebsiteBundle:Team')->findByUser($user);
        $team = new Team();
        $name_form = $this->createForm(new TeamNameType(), $team);
        
        return $this->render('IBWWebsiteBundle:Stairs:team.html.twig', 
                   array('teams'       => $teams,
                         'name_form'   => $name_form->createView()));
    }

    /**
     * Adds a user to a team
     * 
     * @param   integer     $id     Id of the team
     * @throws  NotFoundException   If team not found
     * @return  Response    The response contains the content from the template
     */
    public function teamJoinAction($id)
    {
        $flag = 0;
        $user = $this->get('security.context')->getToken()->getUser();
        $team = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team')->findOneById($id);
        if ($team) {
            $user_teams = $this->getDoctrine()->getRepository('IBWWebsiteBundle:UserTeam')->findByTeam($team);
            foreach ($user_teams as $user_team) {
                if ($user_team->getUser()->getId() == $user->getId()) {
                    $error = $this->container->getParameter('message_team_already_joined');
                    $this->get('session')->getFlashBag()->add('errors', $error);
                    $flag = 1;
                }
            }
            if ($flag == 0) {
                $em = $this->getDoctrine()->getManager();
                $user_team = new UserTeam();
                $user_team->setTeam($team);
                $user_team->setUser($user);
                $em->persist($user_team);
                $em->flush();
                $message = $this->container->getParameter('message_team_joined');
                $this->get('session')->getFlashBag()->add('messages', $message);
            }
            return $this->redirect($this->generateUrl('ibw_website_teams_page'));
        }
        throw $this->createNotFoundException('The team does not exist');
    }

    /**
     * Kick a user from a team
     * 
     * @param   integer     $tid    Id of the team
     * @param   integer     $mid    Id of the memeber of team
     * @throws  NotFoundException   If user not found or action is made by a non-owner
     * @return  Response    $response The response contains the content from the template
     */
    public function teamKickAction($mid, $tid)
    {
        $team = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team')->findOneById($tid);
        $user = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User')->findOneById($mid);
        $user_teams = $this->getDoctrine()->getRepository('IBWWebsiteBundle:UserTeam')->findByTeam($team);
        if ($user && $team->getOwner()->getId() == $this->get('security.context')->getToken()->getUser()->getId()) {
            foreach ($user_teams as $user_team) {
                if ($user_team->getUser()->getId() == $user->getId()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($user_team);
                    $em->flush();
                    $message = 'User has been kicked';
                    $this->get('session')->getFlashBag()->add('messages', $message);

                    return $this->redirect($this->generateUrl('ibw_website_team_page', array("id" => $tid)));
                }
            }
        }
        throw $this->createNotFoundException('The user not found');
    }

    /**
     * A user can leave a team
     * 
     * @param   integer     $id     Id of the team
     * @throws  NotFoundException   If team not found or action is made by a non-owner
     * @return  Response    $response The response contains the content from the template
     */
    public function teamLeaveAction($id)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $team = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team')->findOneById($id);
        if ($team) {
            if ($team->getOwner()->getId() == $user->getId()) {
                $message = 'You cannot leave the team until you assign another owner';
                $this->get('session')->getFlashBag()->add('errors', $message);

                return $this->redirect($this->generateUrl('ibw_website_team_page', array("id"        => $id)));
            }
            $user_teams = $this->getDoctrine()->getRepository('IBWWebsiteBundle:UserTeam')->findByTeam($team);
            $em = $this->getDoctrine()->getManager();
            foreach ($user_teams as $user_team) {
                if ($user_team->getUser()->getId() == $user->getId() && $user_team->getTeam()->getId() == $team->getId()) {
                    $em->remove($user_team);
                    $em->flush();
                }
            }
            $message = 'You have left the team';
            $this->get('session')->getFlashBag()->add('messages', $message);

            return $this->redirect($this->generateUrl('ibw_website_teams_page'));
        }
        throw $this->createNotFoundException('The team does not exist');
    }

    /**
     * Asigns another user to be owner of a team
     * 
     * @param   integer     $tid    Id of the team
     * @param   integer     $mid    Id of the memeber of team or action is made by a non-owner
     * @throws  NotFoundException   If user not found
     * @return  Response    $response The response contains the content from the template
     */
    public function teamOwnerAction($mid, $tid)
    {
        $team = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team')->findOneById($tid);
        $user = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User')->findOneById($mid);
        $user_teams = $this->getDoctrine()->getRepository('IBWWebsiteBundle:UserTeam')->findByTeam($team);
        if ($user && $team->getOwner()->getId() == $this->get('security.context')->getToken()->getUser()->getId()) {
            foreach ($user_teams as $user_team) {
                if ($user_team->getUser() == $user) {
                    $em = $this->getDoctrine()->getManager();
                    $team->setOwner($user);
                    $em->flush();
                    $message = 'Owner has been changed';
                    $this->get('session')->getFlashBag()->add('messages', $message);

                    return $this->redirect($this->generateUrl('ibw_website_team_page', array("id" => $tid)));
                }
            }
        }
        throw $this->createNotFoundException('The user not found');
    }

    /**
     * Team idividual page
     * 
     * @param   integer     $id     Id of the team
     * @throws  NotFoundException   If team not found
     * @return  Response    $response The response contains the content from the template
     */
    public function teamPageAction($id)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $team = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team')->findOneById($id);
        if ($team) {
            $user_teams = $this->getDoctrine()->getRepository('IBWWebsiteBundle:UserTeam')->findByTeam($team);
            $users = array();
            foreach ($user_teams as $user) {
                array_push($users, $user->getUser());
            }
            $user_repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
            $all_time_top = $user_repository->getTop(null, null, $team, 10);
            if(date('D') != 'Sun') {
                $this_week_top = $user_repository->getTop(
                        date('Y-m-d 00:01:00', strtotime('Monday this week', time())), null, $team, 10);
            } else {
                $this_week_top = $user_repository->getTop(
                        date('Y-m-d 00:01:00', strtotime('Monday last week', time())), null, $team, 10);
            }
            $this_month_top = $user_repository->getTop(date('Y-m-01 00:01:00'), null, $team, 10);
            if(date('D') != 'Sun') {
                $last_week_top = $user_repository->getTop(
                    date('Y-m-d 00:01:00', strtotime('Monday last week', time())), 
                    date('Y-m-d 23:59:00', strtotime('Sunday last week', time())), $team, 10);
            }
            else {
                $last_week_top = $user_repository->getTop(
                    date('Y-m-d 00:01:00', strtotime('Monday -2 week', time())), 
                    date('Y-m-d 23:59:00', strtotime('Sunday -1 week', time())), $team, 10);
            }
            $last_month_top = $user_repository->getTop(
                    date('Y-m-d 00:01:00', strtotime('First day of last month', time())),
                    date('Y-m-t 23:59:00', strtotime('Last month', time())), $team, 10);
            $name_form = $this->createForm(new TeamNameType(), $team);

            return $this->render('IBWWebsiteBundle:Stairs:teamPage.html.twig', 
                       array('team'           => $team,
                             'members'        => $users,
                             'name_form'      => $name_form->createView(),
                             'all_time_top'   => $all_time_top,
                             'this_week_top'  => $this_week_top,
                             'this_month_top' => $this_month_top,
                             'last_week_top'  => $last_week_top,
                             'last_month_top' => $last_month_top));
        }
        throw $this->createNotFoundException('The team does not exist');
    }

    /**
     * Renames a team
     * 
     * @param    integer     $id     Id of the team
     * @throws   NotFoundException   If team not found or action is made by a non-owner
     * @return   Response    $response   The response contains the content from the template
     */
    public function teamRenameAction($id)
    {
        $request = $this->getRequest();
        $team = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team')->findOneById($id);
        if ($team && $team->getOwner()->getId() == $this->get('security.context')->getToken()->getUser()->getId()) {
            $team_clone = clone $team;
            $em = $this->getDoctrine()->getManager();
            $name_form = $this->createForm(new TeamNameType(), $team);
            $name_form->bindRequest($request);
            if ($name_form->isValid()) {
                $em->flush();
                $message = "Team name updated!";
                $this->get('session')->getFlashBag()->add('messages', $message);

                return $this->redirect($this->generateUrl('ibw_website_team_page', array("id" => $id)));
            } else {
                $user_repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
                $top = new \stdClass();
                $top->all_time_top = $user_repository->getTop(null, null, $team, 10);
                $top->this_week_top = $user_repository->getTop(
                        date('Y-m-d 00:01:00', strtotime('Monday this week', time())), null, $team, 10);
                $top->this_month_top = $user_repository->getTop(date('Y-m-01 00:01:00'), null, $team, 10);
                $top->last_week_top = $user_repository->getTop(
                        date('Y-m-d 00:01:00', strtotime('Monday last week', time())),
                        date('Y-m-d 23:59:00', strtotime('Sunday last week', time())), $team, 10);
                $top->last_month_top = $user_repository->getTop(
                        date('Y-m-d 00:01:00', strtotime('First day of last month', time())),
                        date('Y-m-t 23:59:00', strtotime('Last month', time())), $team, 10);
                $message = "Team name not updated!";
                $this->get('session')->getFlashBag()->add('errors', $message);
                $user_teams = $this->getDoctrine()->getRepository('IBWWebsiteBundle:UserTeam')->findByTeam($team);
                $users = array();
                foreach ($user_teams as $user) {
                    array_push($users, $user->getUser());
                }

                return $this->render('IBWWebsiteBundle:Stairs:teamPage.html.twig', 
                           array('team'           => $team_clone,
                                 'members'        => $users,
                                 'name_form'      => $name_form->createView(),
                                 'all_time_top'   => $top->all_time_top,
                                 'this_week_top'  => $top->this_week_top,
                                 'this_month_top' => $top->this_month_top,
                                 'last_week_top'  => $top->last_week_top,
                                 'last_month_top' => $top->last_month_top));
            }
        }
        throw $this->createNotFoundException('The team not found');
    }

    /**
     * Search for teams by name
     * 
     * @return Response The response contains the content from the template
     */
    public function teamSearchAction()
    {
        $request = $this->getRequest();
        $name = $request->request->get('name');
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('IBWWebsiteBundle:Team');
        $steams = $repository->searchByName($name);

        return $this->render('IBWWebsiteBundle:Stairs:teamSearch.html.twig', array('searchTeams' => $steams));
    }

    /**
     * Renders a general top page
     * 
     * @return Response The response contains the content from the template
     */
    public function topIndexAction()
    {
        $request = $this->getRequest();
        $custom = '';
        $start_date = $request->request->get('start_date');
        $end_date = $request->request->get('end_date');
        $user_repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $all_time_top = $user_repository->getTop(null, null, null, 10);
        if(date('D') != 'Sun') {
            $this_week_top = $user_repository->getTop(
                    date('Y-m-d 00:01:00', strtotime('Monday this week', time())), null, null, 10);
        } else {
            $this_week_top = $user_repository->getTop(
                    date('Y-m-d 00:01:00', strtotime('Monday last week', time())), null, null, 10);
        }
        $this_month_top = $user_repository->getTop(date('Y-m-01 00:01:00'), null, null, 10);
        if(date('D') != 'Sun') {
            $last_week_top = $user_repository->getTop(
                date('Y-m-d 00:01:00', strtotime('Monday last week', time())), 
                date('Y-m-d 23:59:00', strtotime('Sunday last week', time())), null, 10);
        }
        else {
            $last_week_top = $user_repository->getTop(
                date('Y-m-d 00:01:00', strtotime('Monday -2 week', time())), 
                date('Y-m-d 23:59:00', strtotime('Sunday -1 week', time())), null, 10);
        }
        $last_month_top = $user_repository->getTop(
                date('Y-m-d 00:01:00', strtotime('First day of last month', time())),
                date('Y-m-t 23:59:00', strtotime('Last month', time())), null, 10);
        if ($start_date || $end_date) {
            $custom = $user_repository->getTop($start_date, $end_date, null, 10);
        }

        return $this->render('IBWWebsiteBundle:Stairs:generalTop.html.twig', 
                   array('all_time_top'   => $all_time_top,
                         'this_week_top'  => $this_week_top,
                         'this_month_top' => $this_month_top,
                         'last_week_top'  => $last_week_top,
                         'last_month_top' => $last_month_top,
                         'ctop'           => $custom));
    }

    /**
     * Page where user can change password is rendered
     * 
     * @return Response The response contains the content from the template
     */
    public function userSettingsAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $new_password = $request->request->get('password');
            $user = $this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $user->setPassword($new_password);
            $validator = $this->get('validator');
            $errors = $validator->validate($user);
            if (count($errors) == 0) {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($new_password, $user->getSalt());
                $user->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->get('session')->getFlashBag()->add('messages', $this->container->getParameter('message_user_updated'));

                return $this->redirect($this->generateUrl('ibw_website_user_settings'));
            } else {
                foreach ($errors as $error) {
                    $this->get('session')->getFlashBag()->add('errors', $error->getMessage());
                }
            }
        }
        
        return $this->render('IBWWebsiteBundle:Stairs:accsettings.html.twig');
    }

}

