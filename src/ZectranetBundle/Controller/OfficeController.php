<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\OfficePost;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OfficeController extends Controller
{
    /**
     * @Route("/office/{office_id}")
     * @Security("has_role('ROLE_USER')")
     * @param int $office_id
     * @return Response
     */
    public function indexAction($office_id) {
        $office = $this->getDoctrine()->getRepository('ZectranetBundle:Office')->find($office_id);
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->getAssignedOffices()->contains($office) && !$user->getOwnedOffices()->contains($office)) {
            return $this->redirectToRoute('zectranet_user_home');
        }

        $this->get('zectranet.notifier')->clearNotificationsByOfficeId($office_id);

        return $this->render('@Zectranet/office.html.twig', array('office' => $office));
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

        /** @var Office $office */
        $office = Office::addNewOffice($em, $this->getUser(), $name, $description);
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

        if ($office && ($office->getOwnerid() == $user->getId() || $auth_checker->isGranted('ROLE_ADMIN'))) {
            Office::deleteOffice($em, $office_id);
            $this->get('zectranet.notifier')->clearAllNotificationsByOfficeId($office_id);
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
     * @Route("/office/{office_id}/getMembers")
     * @Security("has_role('ROLE_USER')")
     * @param int $office_id
     * @return Response
     */
    public function getMembersAction($office_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $jsonOfficeUsers = Office::getJsonOfficeMembers($em, $office_id);
        $jsonNotOfficeUsers = Office::getJsonNotOfficeMembers($em, $office_id);

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
        foreach ($data['users'] as $user) {
            $user = (object) $user;
            $ids[] = $user->id;
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $office = $em->getRepository('ZectranetBundle:Office')->find($office_id);
        $users = $em->getRepository('ZectranetBundle:User')->findBy(array('id' => $ids));
        $office->setUsers($users);

        $em->persist($office);
        $em->flush();

        $response = new Response(json_encode(array('success' => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
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
            OfficePost::addNewPost($em, $user_id, $office_id, $message);
        }
        return $this->redirectToRoute('zectranet_show_office', array('office_id' => $office_id));
    }
}