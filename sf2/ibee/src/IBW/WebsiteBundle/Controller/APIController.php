<?php

namespace IBW\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IBW\WebsiteBundle\Entity\User;
use IBW\WebsiteBundle\Entity\StairsActivity;
use IBW\WebsiteBundle\Entity\UserTeam;
use IBW\WebsiteBundle\Entity\Team;
use Symfony\Component\HttpFoundation\Response;

/**
 * ApiController Class contains functions for the Stairs Application API
 */
class APIController extends Controller
{

    /**
     * Adds a new activity to database
     * 
     * @return Response The response contains a json encoded object
     *  {"result": "result", "message": "result_message"}
     */
    public function addStairActivityAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $email = $request->query->get('email');
            $password = $request->query->get('password');
            $amount = $request->query->get('amount');
            $createdAt = $request->query->get('created_at');
            $lng = $request->query->get('lng');
            $lat = $request->query->get('lat');
        }
        if ($request->getMethod() == "POST" || $request->getMethod() == "PUT") {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $amount = $request->request->get('amount');
            $createdAt = $request->request->get('created_at');
            $lng = $request->request->get('lng');
            $lat = $request->request->get('lat');
        }
        $responseObject = new \stdClass();
        $factory = $this->get('security.encoder_factory');
        $stairActivity = new StairsActivity();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $user = $repository->findOneByEmail($email);
        if ($user != null) {
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            if ($user->getPassword() == $password) {
                $stairActivity->setUser($user);
                $stairActivity->setAmount($amount);
                if ($lng !== null && $lat !== null) {
                    $stairActivity->setLng($lng);
                    $stairActivity->setLat($lat);
                } else {
                    $stairActivity->setLng(null);
                    $stairActivity->setLat(null);
                }
                $date = \DateTime::createFromFormat('Y-m-d H:i:s', $createdAt);
                if ($date) {
                    $stairActivity->setCreatedAt($date);
                } elseif ($date === false && $createdAt !== null) {
                    $responseObject->result = $this->container->getParameter('result_error');
                    $responseObject->message = $this->container->getParameter('message_wrong_date');
                    $response = new Response(json_encode($responseObject));
                    $response->headers->set('Content-Type', 'application/json');

                    return $response;
                }
                $validator = $this->get('validator');
                $errors = $validator->validate($stairActivity);
                if (count($errors) == 0) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($stairActivity);
                    $em->flush();
                    $responseObject->id = $stairActivity->getId();
                    $responseObject->result = $this->container->getParameter('result_success');
                    $responseObject->message = $this->container->getParameter('message_activity_created');
                } else {
                    $responseObject->result = $this->container->getParameter('result_error');
                    foreach ($errors as $error) {
                        $responseObject->message .= $error->getMessage() . ' ';
                    }
                }
            } else {
                $responseObject->result = $this->container->getParameter('result_error');
                $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            }
        } else {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
        }
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Creates a team 
     * 
     * @return Response The response contains a json encoded object 
     *  {"result": "result", "message": "result_message"}
     */
    public function createTeamAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $email = $request->query->get('email');
            $password = $request->query->get('password');
            $name = $request->query->get('name');
        }
        if ($request->getMethod() == "POST") {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $name = $request->request->get('name');
        }
        $responseObject = new \stdClass();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $user = $repository->findOneByEmail($email);
        if (!$user) {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            $response = new Response(json_encode($responseObject));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } else {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            $em = $this->getDoctrine()->getManager();
            if ($user->getPassword() == $password) {
                $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team');
                $team = $repository->findOneByName($name);
                if (!$team) {
                    $team = new Team();
                    $user_team = new UserTeam();
                    $team->setName($name);
                    $team->setOwner($user);
                    $user_team->setTeam($team);
                    $user_team->setUser($user);
                    $validator = $this->get('validator');
                    $errors = $validator->validate($team);
                    if (count($errors) == 0) {
                        $em->persist($team);
                        $em->persist($user_team);
                        $em->flush();
                        $responseObject->result = $this->container->getParameter('result_success');
                        $responseObject->message = $this->container->getParameter('message_team_created');
                    } else {
                        $responseObject->result = $this->container->getParameter('result_error');
                        foreach ($errors as $error) {
                            $responseObject->message .= $error->getMessage() . ' ';
                        }
                    }
                } else {
                    $responseObject->result = $this->container->getParameter('result_error');
                    $responseObject->message = $this->container->getParameter('team_name_taken');
                }
            } else {
                $responseObject->result = $this->container->getParameter('result_error');
                $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            }
        }
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Creates a new user
     * 
     * @return Response The response contains a json encoded object
     *  {"result": "result", "message": "result_message"}
     */
    public function createUserAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $email = $request->query->get('email');
            $password = $request->query->get('password');
        }
        if ($request->getMethod() == "POST" || $request->getMethod() == "PUT") {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
        }
        $responseObject = new \stdClass();
        $factory = $this->get('security.encoder_factory');
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $user = $repository->findOneByEmail($email);
        if ($user != null) {
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            if ($user->getPassword() == $password) {
                $responseObject->result = $this->container->getParameter('result_exists');
                $responseObject->message = $this->container->getParameter('message_user_allready_created');
                $response = new Response(json_encode($responseObject));
                $response->headers->set('Content-Type', 'application/json');

                return $response;
            }
        }
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);
        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) == 0) {
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            $user->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $responseObject->result = $this->container->getParameter('result_success');
            $responseObject->message = $this->container->getParameter('message_user_created');
        } else {
            $responseObject->result = $this->container->getParameter('result_error');
            foreach ($errors as $error) {
                if ($error->getMessagePluralization() !== null) {
                    $responseObject->message .= $this->get('translator')->transChoice($error->getMessage(),
                            $error->getMessagePluralization(), $error->getMessageParameters()) . ' ';
                } else {
                    $responseObject->message .= $this->get('translator')->trans($error->getMessage()) . ' ';
                }
            }
        }
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Flags an activity for deletion
     * 
     * @return Response The response contains a json encoded object 
     *  {"result": "result", "message": "result_message"}
     */
    public function deleteStairActivityAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $email = $request->query->get('email');
            $password = $request->query->get('password');
            $id = $request->query->get('id');
        }
        if ($request->getMethod() == "POST" || $request->getMethod() == 'DELETE') {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $id = $request->request->get('id');
        }
        $responseObject = new \stdClass();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $user = $repository->findOneByEmail($email);
        if ($user != null) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            if ($user->getPassword() == $password) {
                $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:StairsActivity');
                $stairActivity = $repository->findOneById($id);
                if ($stairActivity) {
                    if ($stairActivity->getUser() == $user) {
                        $stairActivity->setIsDeleted(true);
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        $responseObject->result = $this->container->getParameter('result_success');
                        $responseObject->message = $this->container->getParameter('message_activity_deletion');
                    } else {
                        $responseObject->result = $this->container->getParameter('result_error');
                        $responseObject->message = $this->container->getParameter('message_no_activity_found');
                    }
                } else {
                    $responseObject->result = $this->container->getParameter('result_error');
                    $responseObject->message = $this->container->getParameter('message_no_activity_found');
                }
            } else {
                $responseObject->result = $this->container->getParameter('result_error');
                $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            }
        } else {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
        }
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Edits an activity from database
     * 
     * @return Response The response contains a json encoded object
     *   {"result": "result", "message": "result_message", "id": "activity_id"}
     */
    public function editStairActivityAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $email = $request->query->get('email');
            $password = $request->query->get('password');
            $id = $request->query->get('id');
            $new_amount = $request->query->get('amount');
            $lng = $request->query->get('lng');
            $lat = $request->query->get('lat');
        }
        if ($request->getMethod() == "POST") {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $id = $request->request->get('id');
            $new_amount = $request->request->get('amount');
            $lng = $request->request->get('lng');
            $lat = $request->request->get('lat');
        }
        $responseObject = new \stdClass();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $user = $repository->findOneByEmail($email);
        if (!$user) {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            $response = new Response(json_encode($responseObject));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } else {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            if ($user->getPassword() == $password) {
                $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:StairsActivity');
                $stairActivity = $repository->findOneById($id);
                if ($stairActivity) {
                    if ($stairActivity->getUser() == $user) {
                        $stairActivity->setAmount($new_amount);
                        if ($lng && $lat) {
                            $stairActivity->setlng($lng);
                            $stairActivity->setLat($lat);
                        } else {
                            $stairActivity->setlng(null);
                            $stairActivity->setLat(null);
                        }
                        $validator = $this->get('validator');
                        $errors = $validator->validate($stairActivity);
                        if (count($errors) == 0) {
                            $em = $this->getDoctrine()->getManager();
                            $em->flush();
                            $responseObject->id = $stairActivity->getId();
                            $responseObject->result = $this->container->getParameter('result_success');
                            $responseObject->message = $this->container->getParameter('message_activity_updated');
                        } else {
                            $responseObject->result = $this->container->getParameter('result_error');
                            foreach ($errors as $error) {
                                $responseObject->message .= $error->getMessage() . ' ';
                            }
                        }
                    } else {
                        $responseObject->result = $this->container->getParameter('result_error');
                        $responseObject->message = $this->container->getParameter('message_no_activity_found');
                    }
                } else {
                    $responseObject->result = $this->container->getParameter('result_error');
                    $responseObject->message = $this->container->getParameter('message_no_activity_found');
                }
            } else {
                $responseObject->result = $this->container->getParameter('result_error');
                $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            }
        }
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Edits a user's password
     * 
     * @return Response The response contains a json encoded object
     *  {"result": "result", "message": "result_message"}
     */
    public function editUserAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $email = $request->query->get('email');
            $password = $request->query->get('password');
            $new_password = $request->query->get('new_password');
        }
        if ($request->getMethod() == "POST") {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $new_password = $request->request->get('new_password');
        }
        $responseObject = new \stdClass();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $user = $repository->findOneByEmail($email);
        if (!$user) {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            $response = new Response(json_encode($responseObject));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } else {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            if ($user->getPassword() == $password) {
                $em = $this->getDoctrine()->getManager();
                $user->setPassword($new_password);
                $validator = $this->get('validator');
                $errors = $validator->validate($user);
                if (count($errors) == 0) {
                    $user->setPassword($encoder->encodePassword($new_password, $user->getSalt()));
                    $em->flush();
                    $responseObject->result = $this->container->getParameter('result_success');
                    $responseObject->message = $this->container->getParameter('message_user_updated');
                } else {
                    $responseObject->result = $this->container->getParameter('result_error');
                    foreach ($errors as $error) {
                        if ($error->getMessagePluralization()) {
                            $responseObject->message .= $this->get('translator')->transChoice($error->getMessage(),
                                    $error->getMessagePluralization(), $error->getMessageParameters()) . ' ';
                        } else {
                            $responseObject->message .= $this->get('translator')->trans($error->getMessage()) . ' ';
                        }
                    }
                }
            } else {
                $responseObject->result = $this->container->getParameter('result_error');
                $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            }
        }
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Returns all actions from database for one user
     * 
     * @return Response The response contains a json encoded object 
     * {"result": "result", "message": "result_message",
     * [{"activityObject": "id" = "id", "amount" = "amount", "created_at" = "create_date"}]}
     */
    public function getStairActivityAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $email = $request->query->get('email');
            $password = $request->query->get('password');
            $limit = $request->query->get('limit');
        }
        if ($request->getMethod() == "POST") {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $limit = $request->request->get('limit');
        }
        $limit = abs($limit);
        $responseObject = new \stdClass();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $user = $repository->findOneByEmail($email);
        if ($user != null) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            if ($user->getPassword() == $password) {
                $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:StairsActivity');
                $stairsActivities = $repository->findByUser($user, $limit);
                $responseObject->activities = array();
                foreach ($stairsActivities as $stairActivity) {
                    $activityObject = new \stdClass();
                    $activityObject->id = $stairActivity->getId();
                    $activityObject->amount = $stairActivity->getAmount();
                    $activityObject->created_at = $stairActivity->getCreatedAt()->format('Y-m-d H:i:s');
                    $activityObject->lng = $stairActivity->getLng();
                    $activityObject->lat = $stairActivity->getLat();
                    array_push($responseObject->activities, $activityObject);
                }
                $responseObject->result = $this->container->getParameter('result_success');
                $responseObject->message = $this->container->getParameter('message_activities_returned');
            } else {
                $responseObject->result = $this->container->getParameter('result_error');
                $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            }
        } else {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
        }
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Return team members
     * 
     * @return Response The response contains a json encoded object 
     * {"members": "email_array", "result": "result", "message": "result_message",}
     */
    public function getTeamMembersAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $id = $request->query->get('id');
        }
        if ($request->getMethod() == "POST") {
            $id = $request->request->get('id');
        }
        $responseObject = new \stdClass();
        $team = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team')->find($id);
        if (!$team) {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_team_no_members');
            $response = new Response(json_encode($responseObject));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        $users = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User')->findByTeam($team);
        if (!$users) {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_team_no_members');
            $response = new Response(json_encode($responseObject));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        $responseObject->members = array();
        foreach ($users as $member) {
            array_push($responseObject->members, $member);
        }
        $responseObject->result = $this->container->getParameter('result_success');
        $responseObject->message = $this->container->getParameter('message_team_returned');
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Return a ordered list of users and their amounts of stairs climbed between 2 dates
     * 
     * @return Response The response contains a json encoded object
     * {"result": "result", "message": "result_message",[{"activityObject": "top" = "user record"}]}
     */
    public function getTopAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $start_date = $request->query->get('start_date');
            $end_date = $request->query->get('end_date');
            $team_id = $request->query->get('team');
            $limit = $request->query->get('limit');
        }
        if ($request->getMethod() == "POST") {
            $start_date = $request->request->get('start_date');
            $end_date = $request->request->get('end_date');
            $team_id = $request->request->get('team');
            $limit = $request->request->get('limit');
        }
        $responseObject = new \stdClass();
        $team = null;
        if ($team_id) {
            $team = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team')->find($team_id);
            if (!$team) {
                $responseObject->result = $this->container->getParameter('result_error');
                $responseObject->message = $this->container->getParameter('message_team_no_members');
                $response = new Response(json_encode($responseObject));
                $response->headers->set('Content-Type', 'application/json');

                return $response;
            }
        }
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $top = $repository->getTop($start_date, $end_date, $team, $limit);
        $responseObject->top = array();
        foreach ($top as $activity) {
            array_push($responseObject->top, $activity);
        }
        $responseObject->result = $this->container->getParameter('result_success');
        $responseObject->message = $this->container->getParameter('message_activities_returned');
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /*
     * Return User's stats
     * 
     * @return Response The response contains a json encoded object 
     * {"stats": "no_stairs", "result": "result", "message": "result_message"}
     */

    public function getUserStatsAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $email = $request->query->get('email');
            $password = $request->query->get('password');
            $start_date = $request->query->get('start_date');
            $end_date = $request->query->get('end_date');
        }
        if ($request->getMethod() == "POST") {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $start_date = $request->request->get('start_date');
            $end_date = $request->request->get('end_date');
        }
        $responseObject = new \stdClass();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $user = $repository->findOneByEmail($email);
        if (!$user) {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            $response = new Response(json_encode($responseObject));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } else {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            if ($user->getPassword() == $password) {
                $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
                $responseObject->total = $repository->getAmountForUser($user, $start_date, $end_date);
                $responseObject->result = $this->container->getParameter('result_success');
                $responseObject->message = $this->container->getParameter('message_activities_returned');
            } else {
                $responseObject->result = $this->container->getParameter('result_error');
                $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            }
        }
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Return the teams of which a user is a part of
     * 
     * @return Response The response contains a json encoded object 
     * {"teams": "team_array", "result": "result", "message": "result_message",}
     */
    public function getUserTeamsAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $email = $request->query->get('email');
            $password = $request->query->get('password');
            $belongs = $request->query->get('belongs', true);
        }
        if ($request->getMethod() == "POST") {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $belongs = $request->request->get('belongs', true);
        }
        $responseObject = new \stdClass();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $user = $repository->findOneByEmail($email);
        if (!$user) {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            $response = new Response(json_encode($responseObject));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } else {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            if ($user->getPassword() == $password) {
                $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team');
                if ($belongs) {
                    $teams = $repository->findByUser($user);
                } else {
                    $teams = $repository->findAll();
                }
                $responseObject->teams = array();
                foreach ($teams as $team) {
                    array_push($responseObject->teams, array(
                        "id"                     => $team->getId(),
                        "name"                   => $team->getName(),
                        "is_my_team"             => ($belongs ? true : $team->hasUser($user))));
                }
                $responseObject->result = $this->container->getParameter('result_success');
                $responseObject->message = $this->container->getParameter('message_teams_returned');
            } else {
                $responseObject->result = $this->container->getParameter('result_error');
                $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            }
            $response = new Response(json_encode($responseObject));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
    }

    /**
     * Joins a User in teams
     * 
     * @return Response The response contains a json encoded object 
     *  {"result": "result", "message": "result_message"}
     */
    public function joinTeamAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $email = $request->query->get('email');
            $password = $request->query->get('password');
            $id = $request->query->get('id');
        }
        if ($request->getMethod() == "POST") {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $id = $request->request->get('id');
        }
        $responseObject = new \stdClass();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $user = $repository->findOneByEmail($email);
        if (!$user) {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            $response = new Response(json_encode($responseObject));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } else {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            if ($user->getPassword() == $password) {
                $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team');
                $team = $repository->findOneById($id);
                if ($team) {
                    $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:UserTeam');
                    $user_teams = $repository->findByTeam($team);
                    foreach ($user_teams as $user_team) {
                        if ($user_team->getUser() == $user) {
                            $responseObject->result = $this->container->getParameter('result_error');
                            $responseObject->message = $this->container->getParameter('message_team_already_joined');
                            $response = new Response(json_encode($responseObject));
                            $response->headers->set('Content-Type', 'application/json');

                            return $response;
                        }
                    }
                    $em = $this->getDoctrine()->getManager();
                    $user_team = new UserTeam();
                    $user_team->setTeam($team);
                    $user_team->setUser($user);
                    $em->persist($user_team);
                    $em->flush();
                    $responseObject->result = $this->container->getParameter('result_success');
                    $responseObject->message = $this->container->getParameter('message_team_joined');
                } else {
                    $responseObject->result = $this->container->getParameter('result_error');
                    $responseObject->message = $this->container->getParameter('team_name_not_found');
                }
            } else {
                $responseObject->result = $this->container->getParameter('result_error');
                $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            }
        }
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Returns a team's total amount of stairs climbed
     * 
     * @return Response The response contains a json encoded object 
     *  {"total": "total_number", "result": "result", "message": "result_message"}
     */
    public function teamStatsAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $id = $request->query->get('id');
            $start_date = $request->query->get('start_date');
            $end_date = $request->query->get('end_date');
        }
        if ($request->getMethod() == "POST") {
            $id = $request->request->get('id');
            $start_date = $request->request->get('start_date');
            $end_date = $request->request->get('end_date');
        }
        $responseObject = new \stdClass();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team');
        $team = $repository->findOneById($id);
        if ($team != null) {
            $stats = $repository->getAmountForTeam($team, $start_date, $end_date);
            if ($stats == null) {
                $responseObject->result = $this->container->getParameter('result_success');
                $responseObject->message = $this->container->getParameter('message_stats_returned');
                $responseObject->total = 0;
            } else {
                $responseObject->result = $this->container->getParameter('result_success');
                $responseObject->message = $this->container->getParameter('message_stats_returned');
                $responseObject->total = $stats;
            }
        } else {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('team_name_not_found');
        }
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * A member can request his removal from a team
     * 
     * @return Response The response contains a json encoded object 
     *  {"result": "result", "message": "result_message"}
     */
    public function leaveTeamAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == "GET") {
            $email = $request->query->get('email');
            $password = $request->query->get('password');
            $id = $request->query->get('id');
        }
        if ($request->getMethod() == "POST") {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $id = $request->request->get('id');
        }
        $responseObject = new \stdClass();
        $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:User');
        $user = $repository->findOneByEmail($email);
        if (!$user) {
            $responseObject->result = $this->container->getParameter('result_error');
            $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            $response = new Response(json_encode($responseObject));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } else {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($password, $user->getSalt());
            if ($user->getPassword() == $password) {
                $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:Team');
                $team = $repository->findOneById($id);
                if ($team) {
                    $repository = $this->getDoctrine()->getRepository('IBWWebsiteBundle:UserTeam');
                    $user_teams = $repository->findByTeam($team);
                    $em = $this->getDoctrine()->getManager();
                    foreach ($user_teams as $user_team) {
                        if ($user_team->getUser() == $user) {
                            if ($team->getOwner() != $user) {
                                $em->remove($user_team);
                                $em->flush();
                                $responseObject->result = $this->container->getParameter('result_success');
                                $responseObject->message = $this->container->getParameter('message_team_left');
                                $response = new Response(json_encode($responseObject));
                                $response->headers->set('Content-Type', 'application/json');

                                return $response;
                            } else {
                                $responseObject->result = $this->container->getParameter('result_error');
                                $responseObject->message = $this->container->getParameter('message_team_left_owner');
                                $response = new Response(json_encode($responseObject));
                                $response->headers->set('Content-Type', 'application/json');

                                return $response;
                            }
                        }
                    }
                    $responseObject->result = $this->container->getParameter('result_error');
                    $responseObject->message = $this->container->getParameter('message_team_no_user');
                } else {
                    $responseObject->result = $this->container->getParameter('result_error');
                    $responseObject->message = $this->container->getParameter('team_name_not_found');
                }
            } else {
                $responseObject->result = $this->container->getParameter('result_error');
                $responseObject->message = $this->container->getParameter('message_wrong_email_or_password');
            }
        }
        $response = new Response(json_encode($responseObject));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
   
}

