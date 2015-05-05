<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ZectranetBundle\Entity\EntityOperations;
use ZectranetBundle\Entity\ForgotPassword;
use ZectranetBundle\Entity\HFHeader;
use ZectranetBundle\Entity\HFForum;
use ZectranetBundle\Entity\HFSubHeader;
use ZectranetBundle\Entity\HFThread;
use ZectranetBundle\Entity\HFThreadPost;
use ZectranetBundle\Entity\User;
use ZectranetBundle\Entity\Request as Req;

class HeaderForumController extends Controller {
    /**
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function indexAction($project_id) {
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HFForum')->find($project_id);
        /** @var User $user */
        $user = $this->getUser();
        // If user not in forum or forum is archived then redirect to home office
        if (!$forum->getUsers()->contains($user) || $forum->getArchived()) {
            return $this->redirectToRoute('zectranet_show_office', array('office_id' => $user->getHomeOfficeID()));
        }
        return $this->render('@Zectranet/headerForum.html.twig', array('forum' => $forum));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $project_id
     * @return RedirectResponse
     */
    public function editNameAction(Request $request, $project_id)
    {
        $newName = $request->request->get('newName');

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var HFForum $project */
        $project = $em->getRepository('ZectranetBundle:HFForum')->find($project_id);

        $project->setName($newName);
        $em->flush();

        return $this->redirectToRoute('zectranet_show_header_forum', array('project_id' => $project_id));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @param int $subheader_id
     * @return Response
     */
    public function forumAction($project_id, $subheader_id) {
        /** @var HFForum $forum */
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HFForum')->find($project_id);
        /** @var HFSubHeader $subheader */
        $subheader = $this->getDoctrine()->getRepository('ZectranetBundle:HFSubHeader')->find($subheader_id);
        /** @var User $user */
        $user = $this->getUser();
        // If user not in forum or forum is archived then redirect to home office
        if (!$forum->getUsers()->contains($user) || $forum->getArchived()) {
            return $this->redirectToRoute('zectranet_show_office', array('office_id' => $user->getHomeOfficeID()));
        }
        return $this->render('@Zectranet/headerForumSubHeader.html.twig', array(
            'forum' => $forum,
            'sub' => $subheader,
        ));
    }

    public function editNameSubheaderAction(Request $request, $project_id, $subheader_id)
    {
        $newName = $request->request->get('newName');

        /** @var User $user */
        $user = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var HFSubHeader $subheader */
        $subheader = $em->getRepository('ZectranetBundle:HFSubHeader')->find($subheader_id);

        $subheader->setTitle($newName);
        $em->flush();

        return $this->redirectToRoute('zectranet_show_header_forum_subheader', array('project_id' => $project_id, 'subheader_id' => $subheader_id));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function settingsAction($project_id) {
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HFForum')->find($project_id);
        /** @var User $user */
        $user = $this->getUser();
        // If user not in forum or forum is archived then redirect to home office
        if (!$forum->getUsers()->contains($user) || $forum->getArchived()) {
            return $this->redirectToRoute('zectranet_show_office', array('office_id' => $user->getHomeOfficeID()));
        }
        return $this->render('@Zectranet/headerForumSettings.html.twig', array('forum' => $forum));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return JsonResponse
     */
    public function getHeadersAction($project_id) {
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HFForum')->find($project_id);
        /** @var User $user */
        $user = $this->getUser();
        // If user not in forum or forum is archived then deny access
        if (!$forum->getUsers()->contains($user) || $forum->getArchived()) {
            return new JsonResponse('Not allowed!!!');
        }
        return new JsonResponse(EntityOperations::arrayToJsonArray($forum->getHeaders()));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @return JsonResponse
     */
    public function addNewHeaderAction(Request $request, $project_id) {
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HFForum')->find($project_id);
        /** @var User $user */
        $user = $this->getUser();
        // If user not in forum or forum is archived then deny access
        if (!$forum->getUsers()->contains($user) || $forum->getArchived()) {
            return new JsonResponse('Not allowed!!!');
        }
        $data = json_decode($request->getContent(), true);
        $params = array(
            'title' => $data['header']['title'],
            'bgColor' => $data['header']['bgColor'],
            'textColor' => $data['header']['textColor'],
        );
        /** @var User $user */
        $user = $this->getUser();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        try {
            HFHeader::addNewHeader($em, $project_id, $params);
            $logMessage = 'User "' . $user->getUsername() . '" add new header "'
                . $params['title'] . '"';
            $this->get('zectranet.projectlogger')->logEvent($logMessage, $project_id, 2);
        } catch (\Exception $ex) {
            $from = "Class: HFHeader, function: addNewHeader";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HFForum')->find($project_id);
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
        /** @var User $user */
        $user = $this->getUser();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $header = $em->getRepository('ZectranetBundle:HFHeader')->find($header_id);
        $forum = $header->getForum();
        /** @var User $user */
        $user = $this->getUser();
        // If user not in forum or forum is archived then deny access
        if (!$forum->getUsers()->contains($user) || $forum->getArchived()) {
            return new JsonResponse('Not allowed!!!');
        }
        try {
            HFHeader::addNewSubHeader($em, $params);
            $logMessage = 'User "' . $user->getUsername() . '" add new subheader "'
                . $params['title'] . '"';
            $this->get('zectranet.projectlogger')->logEvent($logMessage, $header->getForumID(), 2);
        } catch (\Exception $ex) {
            $from = "Class: HFHeader, function: addNewSubHeader";
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
        $header = $em->getRepository('ZectranetBundle:HFHeader')->find($header_id);
        $headerName = $header->getTitle();
        $forum = $header->getForum();
        /** @var User $user */
        $user = $this->getUser();
        // If user not in forum or forum is archived then deny access
        if (!$forum->getUsers()->contains($user) || $forum->getArchived()) {
            return new JsonResponse('Not allowed!!!');
        }
        try {
            HFHeader::deleteHeader($em, $header_id);
            $logMessage = 'User "' . $user->getUsername() . '" delete header "'
                . $headerName . '"';
            $this->get('zectranet.projectlogger')->logEvent($logMessage, $header->getForumID(), 2);
        } catch (\Exception $ex) {
            $from = "Class: HFHeader, function: deleteHeader";
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
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:HFForum')->find($project_id);
        $subHeader = $this->getDoctrine()->getRepository('ZectranetBundle:HFSubHeader')->find($subheader_id);
        $thread = $this->getDoctrine()->getRepository('ZectranetBundle:HFThread')->find($thread_id);

        /** @var User $user */
        $user = $this->getUser();
        // If user not in forum or forum is archived then redirect to home office
        if (!$forum->getUsers()->contains($user) || $forum->getArchived()) {
            return $this->redirectToRoute('zectranet_show_office', array('office_id' => $user->getHomeOfficeID()));
        }

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
        $subHeader = $em->getRepository('ZectranetBundle:HFSubHeader')->find($subheader_id);
        /** @var User $user */
        $user = $this->getUser();
        $forum = $subHeader->getHeader()->getForum();
        // If user not in forum or forum is archived then redirect to home office
        if (!$forum->getUsers()->contains($user) || $forum->getArchived()) {
            return $this->redirectToRoute('zectranet_show_office', array('office_id' => $user->getHomeOfficeID()));
        }

        $params = array(
            'title' => $request->request->get('title'),
            'message' => $request->request->get('message'),
            'keywords' => $request->request->get('keywords'),
        );

        $thread = null;
        try {
            $thread = HFThread::startNewThread($em, $subheader_id, $user->getId(), $params);
            $logMessage = 'User "' . $user->getUsername() . '" start new thread "'
                . $thread->getTitle() . '"';
            $this->get('zectranet.projectlogger')->logEvent($logMessage, $subHeader->getHeader()->getForumID(), 2);
        } catch (\Exception $ex) {
            $from = "Class: HFThread, function: startNewThread";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return $this->redirectToRoute('zectranet_show_header_forum_subheader', array(
                'project_id' => $forum->getId(),
                'subheader_id' => $subheader_id,
            ));
        }

        return $this->redirectToRoute('zectranet_show_header_forum_thread', array(
            'project_id' => $subHeader->getHeader()->getForumID(),
            'subheader_id' => $subHeader->getId(),
            'thread_id' => $thread->getId(),
        ));
    }

    /**
     * @param Request $request
     * @param int $thread_id
     * @return RedirectResponse
     */
    public function addNewPostAction(Request $request, $thread_id) {
        $message = $request->request->get('message');
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();

        $thread = $em->getRepository('ZectranetBundle:HFThread')->find($thread_id);
        $forum = $thread->getSubHeader()->getHeader()->getForum();
        // If user not in forum or forum is archived then redirect to home office
        if (!$forum->getUsers()->contains($user) || $forum->getArchived()) {
            return $this->redirectToRoute('zectranet_show_office', array('office_id' => $user->getHomeOfficeID()));
        }
        try {
            $th = HFThreadPost::addNewPost($em, $thread_id, $user->getId(), $message);
        } catch (\Exception $ex) {
            $from = "Class: HFThreadPost, function: addNewPost";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
        }
        return $this->redirectToRoute('zectranet_show_header_forum_thread', array(
            'project_id' => $thread->getSubHeader()->getHeader()->getForumID(),
            'subheader_id' => $thread->getSubHeaderID(),
            'thread_id' => $thread->getId(),
        ));
    }

    /**
     * @param int $project_id
     * @return JsonResponse
     */
    public function getProjectSettingsInfoAction($project_id) {
        $info = array();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $project = $em->find('ZectranetBundle:HFForum', $project_id);
        /** @var User $user */
        $user = $this->getUser();
        // If user not in forum or forum is archived then deny access
        if (!$project->getUsers()->contains($user) || $project->getArchived()) {
            return new JsonResponse('Not allowed!!!');
        }
        $info['HO_Contacts'] = HFForum::getNotProjectHomeOfficeMembers($em, $user->getId(), $project_id);
        $info['All_Contacts'] = HFForum::getNotProjectSiteMembers($em, $project_id);
        $info['Project_Team'] = EntityOperations::arrayToJsonArray(
            $em->getRepository('ZectranetBundle:Request')->findBy(array('HFForumID' => $project_id))
        );
        $info['HFLogs'] = EntityOperations::arrayToJsonArray($project->getLogs());
        $info['timeNow'] = (new \DateTime())->format('Y-m-d H:i:s');

        return new JsonResponse($info);
    }

    /**
     * @param Request $request
     * @param int $project_id
     * @return JsonResponse
     */
    public function sendRequestAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $user_id = $data['user_id'];
        $message = $data['message'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $contact = $em->find('ZectranetBundle:User', $user_id);
        /** @var User $user */
        $user = $this->getUser();
        $project = $em->find('ZectranetBundle:HFForum', $project_id);
        // If user not in forum or forum is archived then deny access
        if (!$project->getUsers()->contains($user) || $project->getArchived()) {
            return new JsonResponse('Not allowed!!!');
        }

        try {
            HFForum::sendRequestToUser($em, $user_id, $project_id, $message, $user->getId());
        } catch (\Exception $ex) {
            $from = 'class: HFForum, function: sendRequestToUser';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
        $logMessage = 'User "' . $user->getUsername() . '" sent project request to user "'
            . $contact->getUsername() . '"';
        $this->get('zectranet.projectlogger')->logEvent($logMessage, $project_id, 2);
        return new JsonResponse(1);
    }

    /**
     * @param Request $request
     * @param int $project_id
     * @return JsonResponse
     */
    public function reSendRequestAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $request_id = $data['id'];
        $user_id = $data['user_id'];
        $message = $data['message'];
        /** @var User $user */
        $user = $this->getUser();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $contact = $em->find('ZectranetBundle:User', $user_id);
        try {
            HFForum::removeRequest($em, $request_id);
            HFForum::sendRequestToUser($em, $user_id, $project_id, $message, $this->getUser()->getId());
        } catch (\Exception $ex) {
            $from = 'class: HeaderForumController, function: reSendRequestAction';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
        $logMessage = 'User "' . $user->getUsername() . '" resent project request to user "'
            . $contact->getUsername() . '"';
        $this->get('zectranet.projectlogger')->logEvent($logMessage, $project_id, 2);

        return new JsonResponse(1);
    }


    /**
     * @param int $project_id
     * @param int $request_id
     * @return JsonResponse
     */
    public function deleteRequestAction($project_id, $request_id) {
        /** @var User $user */
        $user = $this->getUser();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $request = $em->find('ZectranetBundle:Request', $request_id);
        $contact = $em->find('ZectranetBundle:User', $request->getUserid());

        $project = $em->find('ZectranetBundle:HFForum', $project_id);
        // If user not in forum or forum is archived then deny access
        if (!$project->getUsers()->contains($user) || $project->getArchived()) {
            return new JsonResponse('Not allowed!!!');
        }
        try {
            /** @var Req $request */
            $request = HFForum::removeRequest($em, $request_id);
        } catch (\Exception $ex) {
            $from = 'class: HFForum, function: removeRequest';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }

        try {
            HFForum::removeUserFromProject($em, $contact->getId(), $project_id);
            $logMessage = 'User "' . $user->getUsername() . '" remove user "'
                . $request->getUser()->getUsername() . '" from request grid';
            $this->get('zectranet.projectlogger')->logEvent($logMessage, $project_id, 2);
            return new JsonResponse(1);
        } catch (\Exception $ex) {
            $from = 'class: HFForum, function: removeUserFromProject';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
    }
}