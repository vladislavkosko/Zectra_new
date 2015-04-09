<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ZectranetBundle\Entity\EntityOperations;
use ZectranetBundle\Entity\ForgotPassword;
use ZectranetBundle\Entity\Header;
use ZectranetBundle\Entity\HeaderForum;
use ZectranetBundle\Entity\SubHeader;
use ZectranetBundle\Entity\Thread;
use ZectranetBundle\Entity\User;

class HeaderForumController extends Controller {
    /**
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function indexAction($project_id) {
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HeaderForum')->find($project_id);
        return $this->render('@Zectranet/headerForum.html.twig', array('forum' => $forum));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @param int $subheader_id
     * @return Response
     */
    public function forumAction($project_id, $subheader_id) {
        /** @var HeaderForum $forum */
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HeaderForum')->find($project_id);
        /** @var SubHeader $subheader */
        $subheader = $this->getDoctrine()->getRepository('ZectranetBundle:SubHeader')->find($subheader_id);
        return $this->render('@Zectranet/headerForumSubHeader.html.twig', array(
            'forum' => $forum,
            'sub' => $subheader,
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function settingsAction($project_id) {
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HeaderForum')->find($project_id);
        return $this->render('@Zectranet/headerForumSettings.html.twig', array('forum' => $forum));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return JsonResponse
     */
    public function getHeadersAction($project_id) {
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HeaderForum')->find($project_id);
        return new JsonResponse(EntityOperations::arrayToJsonArray($forum->getHeaders()));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @return JsonResponse
     */
    public function addNewHeaderAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $params = array(
            'title' => $data['header']['title'],
            'bgColor' => $data['header']['bgColor'],
            'textColor' => $data['header']['textColor'],
        );
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        try {
            Header::addNewHeader($em, $project_id, $params);
        } catch (\Exception $ex) {
            $from = "Class: Header, function: addNewHeader";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HeaderForum')->find($project_id);
        return new JsonResponse(EntityOperations::arrayToJsonArray($forum->getHeaders()));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $header_id
     * @return JsonResponse
     */
    public function addNewSubHeaderAction(Request $request, $header_id) {
        $data = json_decode($request->getContent(), true);
        $params = array(
            'title' => $data['subheader']['title'],
            'header_id' => $header_id,
            'description' => $data['subheader']['description'],
            'admin' => $data['subheader']['admin'],
        );
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $header = $em->getRepository('ZectranetBundle:Header')->find($header_id);
        try {
            Header::addNewSubHeader($em, $params);
        } catch (\Exception $ex) {
            $from = "Class: Header, function: addNewSubHeader";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }
        return new JsonResponse(EntityOperations::arrayToJsonArray($header->getForum()->getHeaders()));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param int $header_id
     * @return JsonResponse
     */
    public function deleteHeaderAction($header_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $header = $em->getRepository('ZectranetBundle:Header')->find($header_id);
        $forum = $header->getForum();
        try {
            Header::deleteHeader($em, $header_id);
        } catch (\Exception $ex) {
            $from = "Class: Header, function: deleteHeader";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

        return new JsonResponse(EntityOperations::arrayToJsonArray($forum->getHeaders()));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @param int $subheader_id
     * @param int $thread_id
     * @return Response
     */
    public function showThreadAction($project_id, $subheader_id, $thread_id) {
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HeaderForum')->find($project_id);
        $subHeader = $this->getDoctrine()->getRepository('ZectranetBundle:SubHeader')->find($subheader_id);
        $thread = $this->getDoctrine()->getRepository('ZectranetBundle:Thread')->find($thread_id);

        return $this->render('@Zectranet/headerForumThread.html.twig', array(
            'forum' => $forum,
            'sub' => $subHeader,
            'thread' => $thread,
        ));
    }

    /**
     * @param Request $request
     * @param int $subheader_id
     * @return Response
     */
    public function startNewThreadAction(Request $request, $subheader_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $subHeader = $em->getRepository('ZectranetBundle:SubHeader')->find($subheader_id);
        /** @var User $user */
        $user = $this->getUser();
        $params = array(
            'title' => $request->request->get('title'),
            'message' => $request->request->get('message'),
        );

        $thread = null;
        try {
            $thread = Thread::startNewThread($em, $subheader_id, $user->getId(), $params);
        } catch (\Exception $ex) {
            $from = "Class: Thread, function: startNewThread";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return $this->redirectToRoute('zectranet_show_header_forum_subheader', array(
                'project_id' => $subHeader->getHeader()->getForumID(),
                'subheader_id' => $subheader_id,
            ));
        }

        return $this->redirectToRoute('zectranet_show_header_forum_thread', array(
            'project_id' => $subHeader->getHeader()->getForumID(),
            'subheader_id' => $subHeader->getId(),
            'thread_id' => $thread->getId(),
        ));
    }
}