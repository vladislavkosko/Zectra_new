<?php

namespace ZectranetBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ZectranetBundle\Entity\Notification;
use ZectranetBundle\Entity\User;

class NotificationController extends Controller
{
    /**
     * @Route("/notifications")
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function showNotificationsAction()
    {
        return $this->render('@Zectranet/showNotifications.html.twig', array());
    }

    /**
     * @Route("/notifications/get")
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function getNotificationsAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $user_requests = $user->getrequests();

        $user_notifications = Notification::prepareNotifications($user);

        $response = new Response(json_encode(array("result" => array(
            'notifications' => array_map(function($e){return $e->getInArray();}, $user_notifications),
            'requests' => array_map(function($e){return $e->getInArray();}, $user_requests->toArray())
        ))));
    	$response->headers->set('Content-Type', 'application/json');
    	return $response;
    }

    /**
     * @Route("/notifications/clear")
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function clearNotificationsAction()
    {
    	$this->getUser()->clearNotifications($this->getDoctrine()->getManager());
    	return $this->redirect($this->generateUrl('zectranet_notifications_show'));
    }
}
