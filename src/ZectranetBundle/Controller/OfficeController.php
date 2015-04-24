<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use ZectranetBundle\Entity\DailyTimeSheet;
use ZectranetBundle\Entity\Notification;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\OfficePost;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\RequestType;
use ZectranetBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OfficeController extends Controller
{
    /**
     * @Route("/office/{office_id}/{conversation_id}")
     * @Security("has_role('ROLE_USER')")
     * @param $office_id
     * @param null $conversation_id
     * @return RedirectResponse|Response
     */
    public function indexAction($office_id, $conversation_id = null) {
        $office = $this->getDoctrine()->getRepository('ZectranetBundle:Office')->find($office_id);
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->getAssignedOffices()->contains($office) && !$user->getOwnedOffices()->contains($office)) {
            return $this->redirectToRoute('zectranet_user_home');
        }

        if ($office->getId() == $user->getHomeOfficeID())
        {
            return $this->render('@Zectranet/homeOffice.html.twig', array('office' => $office, 'conversation_id' => $conversation_id));
        }
        else
        {
            $this->get('zectranet.notifier')->clearNotificationsByOfficeId($office_id);
            return $this->render('@Zectranet/office.html.twig', array('office' => $office));
        }
    }

    /**
     * @Route("/office/add")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return Response
     */
    public function addOfficeAction(Request $request) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $name = $request->request->get('office_name');
        $description = $request->request->get('office_description');

        $office = null;
        try {
            /** @var Office $office */
            $office = Office::addNewOffice($em, $this->getUser(), $name, $description);
        } catch (\Exception $ex) {
            $from = "Class: Office, function: addNewOffice";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return $this->redirectToRoute('zectranet_show_office', array('office_id' => $office->getId()));
        }
        return $this->redirectToRoute('zectranet_show_office', array('office_id' => $office->getId()));
    }

    /**
     * @Route("/office/{office_id}/delete")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $office_id
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $office_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var AuthorizationChecker $auth_checker */
        $auth_checker = $this->get('security.authorization_checker');

        /** @var Project $project */
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);

        /** @var User $user */
        $user = $this->getUser();

        try {
            if ($office && ($office->getOwnerid() == $user->getId() || $auth_checker->isGranted('ROLE_ADMIN'))) {
                Office::deleteOffice($em, $office_id);
                $this->get('zectranet.notifier')->clearAllNotificationsByOfficeId($office_id);
            }
        } catch (\Exception $ex) {
            $from = "Class: Office, function: deleteOffice";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return $this->redirectToRoute('zectranet_user_page');
        }

        return $this->redirectToRoute('zectranet_user_page');
    }

    /**
     * @Route("/office/{office_id}/settings")
     * @Security("has_role('ROLE_USER')")
     * @param int $office_id
     * @return Response
     */
    public function settingsAction($office_id)
    {
        $office = $this->getDoctrine()->getRepository('ZectranetBundle:Office')->find($office_id);
        return $this->render('@Zectranet/officeSettings.html.twig', array(
            'office' => $office
        ));
    }

    /**
     * @Route("/office/{office_id}/settings/visibleStateChange")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $office_id
     * @return Response
     */
    public function visibleStateChangeAction(Request $request, $office_id)
    {
        $data = json_decode($request->getContent(), true);
        $data = (object)$data;
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);
        $office->setVisible($data->visible);
        $em->persist($office);
        $em->flush();

        $response = new Response(json_encode(array('success' => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/office/{office_id}/getWDE")
     * @Security("has_role('ROLE_USER')")
     * @param $office_id
     * @return bool
     */
    public function getWDEAction($office_id)
    {
        $office = $this->getDoctrine()->getRepository('ZectranetBundle:Office')->find($office_id);
        $office_users_ids = array();
        $office_users_ids[] = $office->getOwner()->getId();
        foreach ($office->getUsers() as $user)
            $office_users_ids[] = $user->getId();

        $office_WDE = $this->getDoctrine()->getRepository('ZectranetBundle:DailyTimeSheet')->findBy(array('userid' => $office_users_ids));

        $WDE = array();
        /** @var DailyTimeSheet $wde */
        foreach ($office_WDE as $wde)
            $WDE[] = $wde->getInArray();

        $response = new Response(json_encode(array(
            'WDE' => $WDE
        )));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/office/{office_id}/showWDE")
     * @Security("has_role('ROLE_USER')")
     * @param $office_id
     * @return Response
     */
    public function showWDEAction($office_id)
    {
        $office = $this->getDoctrine()->getRepository('ZectranetBundle:Office')->find($office_id);
        return $this->render('@Zectranet/WDE.html.twig', array('office' => $office));
    }

    /**
     * @Route("/office/{office_id}/getMembers")
     * @Security("has_role('ROLE_USER')")
     * @param int $office_id
     * @return Response
     */
    public function getMembersAction($office_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $jsonOfficeUsers = null;
        $jsonNotOfficeUsers = null;

        try {
            $jsonOfficeUsers = Office::getJsonOfficeMembers($em, $office_id);
        } catch (\Exception $ex) {
            $from = "Class: Office, function: getJsonOfficeMembers";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

        try {
            $jsonNotOfficeUsers = Office::getJsonNotOfficeMembers($em, $office_id);
        } catch (\Exception $ex) {
            $from = "Class: Office, function: getJsonNotOfficeMembers";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

        $response = new Response(json_encode(array(
            'officeMembers' => $jsonOfficeUsers,
            'users' => $jsonNotOfficeUsers
        )));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/office/{office_id}/saveMembersState")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $office_id
     * @return Response
     */
    public function saveMembersAction(Request $request, $office_id) {
        $data = json_decode($request->getContent(), true);
        $ids = array();
        foreach ($data['users'] as $user)
        {
            if ((!isset($user['request'])) or ((isset($user['request'])) and ($user['request'] == 2)))
            {
                $user = (object) $user;
                $ids[] = $user->id;
            }
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);
        $users = $em->getRepository('ZectranetBundle:User')->findBy(array('id' => $ids));

        if ($data['status'] == 1)
        {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            /** @var RequestType $type */
            $type = $this->getDoctrine()->getRepository('ZectranetBundle:RequestType')->find(1);

            $usersNames = array();
            foreach ($office->getUsers() as $user)
                $usersNames[] = $user->getUsername();

            $usersRequest = array();
            foreach ($users as $user)
            {
                if (!in_array($user->getUsername(), $usersNames))
                    $usersRequest[] = $user;
            }

            try {
                foreach ($usersRequest as $user)
                    \ZectranetBundle\Entity\Request::addNewRequest($em, $user, $type, null, $office);
            } catch (\Exception $ex) {
                $from = "Class: Request, function: addNewRequest";
                $this->get('zectranet.errorlogger')->registerException($ex, $from);
                return new JsonResponse(false);
            }

            /** @var User $user */
            $user = $this->getUser();
            try {
                $this->get('zectranet.notifier')->createNotification("request_office", $user, $usersRequest, $office);
            } catch (\Exception $ex) {
                $from = "Class: zectranet_notifier, function: createNotification";
                $this->get('zectranet.errorlogger')->registerException($ex, $from);
                return new JsonResponse(false);
            }
        }

        if ($data['status'] == 0)
        {
            $office->setUsers($users);
            $em->persist($office);
            $em->flush();
        }

        $response = new Response(json_encode(array('success' => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/office/{office_id}/acceptRequestUserOffice")
     * @Security("has_role('ROLE_USER')")
     * @param $office_id
     * @return RedirectResponse
     */
    public function acceptRequestUserOfficeAction($office_id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        /** @var Project $project */
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);
        /** @var Request $request */
        $request = $em->getRepository('ZectranetBundle:Request')->findOneBy(array('userid' => $user->getId(), 'officeid' => $office_id, 'typeid' => 1));
        /** @var Notification $notification */
        $notification = $em->getRepository('ZectranetBundle:Notification')->findOneBy(array('userid' => $user->getId(), 'destinationid' => $office_id, 'type' => 'request_office'));

        $usersOffice = $office->getUsers();
        $usersOffice[] = $user;

        $office->setUsers($usersOffice);
        $em->persist($office);
        $em->remove($request);
        $em->remove($notification);
        $em->flush();

        return $this->redirectToRoute('zectranet_show_office', array('office_id' => $office_id));
    }

    /**
     * @Route("/office/{office_id}/declineRequestUserOffice")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $office_id
     * @return RedirectResponse
     */
    public function declineRequestUserOfficeAction(Request $request, $office_id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();

        /** @var Request $request */
        $requestUser = $em->getRepository('ZectranetBundle:Request')->findOneBy(array('userid' => $user->getId(), 'officeid' => $office_id, 'typeid' => 1));
        /** @var Notification $notification */
        $notification = $em->getRepository('ZectranetBundle:Notification')->findOneBy(array('userid' => $user->getId(), 'destinationid' => $office_id, 'type' => 'request_office'));

        $em->remove($requestUser);
        if ($notification != null)
            $em->remove($notification);
        $em->flush();

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * @Route("/office/{office_id}/addNewPost")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $office_id
     * @return RedirectResponse
     */
    public function addPostAction(Request $request, $office_id) {
        $message = $request->request->get('message');
        if ($request->getMethod() === "POST" && $message !== '') {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $user_id = $this->getUser()->getId();
            try {
                OfficePost::addNewPost($em, $user_id, $office_id, $message);
            } catch (\Exception $ex) {
                $from = "Class: OfficePost, function: addNewPost";
                $this->get('zectranet.errorlogger')->registerException($ex, $from);
                return new JsonResponse(false);
            }
        }
        return $this->redirectToRoute('zectranet_show_office', array('office_id' => $office_id));
    }
}