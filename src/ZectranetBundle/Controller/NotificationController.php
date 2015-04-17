<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ZectranetBundle\Entity\Notification;
use ZectranetBundle\Entity\User;
use ZectranetBundle\Entity\Request as Req;

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
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $user_requests = Req::getSentRequestsByUserID($em, $user->getId());

        $user_notifications = null;
        try {
            $user_notifications = Notification::prepareNotifications($user);
        } catch (\Exception $ex) {
            $from = "Class: Notification, function: prepareNotifications";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

        $response = new Response(json_encode(array("result" => array(
            'notifications' => array_map(function($e){return $e->getInArray();}, $user_notifications),
            'requests' => $user_requests
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

    /**
     * @Route("user/notifications/all")
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function allAction()
    {
        return $this->render('@Zectranet/notifications.html.twig');
    }

    /**
     * @Route("user/notifications/delete_all")
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function deleteAllAction()
    {
        $this->get('zectranet.notifier')->clearAllNotifications();
        return $this->redirectToRoute('zectranet_all_notifications');
    }
}
