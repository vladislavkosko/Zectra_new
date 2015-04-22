<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ZectranetBundle\Entity\EntityOperations;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\QnAForum;
use ZectranetBundle\Entity\QnAPost;
use ZectranetBundle\Entity\QnAThread;
use ZectranetBundle\Entity\User;
use ZectranetBundle\Entity\Request as Req;

class QnAForumController extends Controller {

    /**
     * @Security("has_role('ROLE_USER')")
     * @param $project_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showForumAction($project_id)
    {
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:QnAForum')->find($project_id);

        $keywords = array();
        /** @var QnAThread $thread */
        foreach ($project->getThreads() as $thread)
            $keywords[]['keys'] = explode(',', $thread->getKeywords());

        return $this->render('ZectranetBundle::QnAForum.html.twig', array('forum' => $project, 'keywords' => $keywords));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param $project_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showProfileAction($project_id)
    {
        /** @var Project $project */
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:QnAForum')->find($project_id);

        return $this->render('@Zectranet/projectProfile.html.twig', array('project' => $project));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param $project_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction($project_id)
    {
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:QnAForum')->find($project_id);
        return $this->render('ZectranetBundle::QnASettings.html.twig', array('forum' => $project));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param $project_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteForumAction($project_id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        $project = $em->getRepository('ZectranetBundle:QnAForum')->find($project_id);
        $em->remove($project);
        $em->flush();

        return $this->redirectToRoute('zectranet_show_office', array('office_id' => $user->getHomeOfficeID()));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param $project_id
     * @param $thread_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showThreadAction($project_id, $thread_id)
    {
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:QnAForum')->find($project_id);

        $thread = $this->getDoctrine()->getRepository('ZectranetBundle:QnAThread')->find($thread_id);

        $keywords = explode(',', $thread->getKeywords());

        return $this->render('ZectranetBundle::QnAThread.html.twig', array('forum' => $project, 'thread' => $thread, 'keywords' => $keywords));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $project_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createQuestionAction(Request $request, $project_id)
    {
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:QnAForum')->find($project_id);
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $parameters = array(
            'title' => $request->request->get('title'),
            'message' => $request->request->get('message'),
            'keywords' => $request->request->get('keywords'),
        );

        $question = null;

        try {
            $question = QnAThread::createNewQuestion($em, $user, $project, $parameters);
        } catch (\Exception $ex) {
            $from = "Class: QnAThread, function: createNewQuestion";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return $this->redirectToRoute('zectranet_show_QnA_forum', array('project_id' => $project->getId()));
        }

        return $this->redirectToRoute('zectranet_show_QnA_thread', array('project_id' => $project->getId(), 'thread_id' => $question->getId()));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $thread_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addPostAction(Request $request, $thread_id)
    {
        $message = $request->request->get('message');
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        /** @var QnAThread $thread */
        $thread = $this->getDoctrine()->getRepository('ZectranetBundle:QnAThread')->find($thread_id);

        try {
            $post = QnAPost::addPost($em, $thread, $user, $message);
        } catch (\Exception $ex) {
            $from = "Class: QnAPost, function: addPost";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
        }

        return $this->redirectToRoute('zectranet_show_QnA_thread', array('project_id' => $thread->getForumID(), 'thread_id' => $thread_id));
    }

    /**
     * @param int $project_id
     * @return JsonResponse
     */
    public function getProjectSettingsInfoAction($project_id) {
        $info = array();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $project = $em->find('ZectranetBundle:QnAForum', $project_id);
        $info['HO_Contacts'] = QnAForum::getNotProjectHomeOfficeMembers($em, $user->getId(), $project_id);
        $info['All_Contacts'] = QnAForum::getNotProjectSiteMembers($em, $project_id);
        $info['Project_Team'] = EntityOperations::arrayToJsonArray(
            $em->getRepository('ZectranetBundle:Request')->findBy(array('QnAForumID' => $project_id))
        );
        $info['QnALogs'] = EntityOperations::arrayToJsonArray($project->getLogs());
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
        /** @var User $user */
        $user = $this->getUser();
        $contact = $em->find('ZectranetBundle:User', $user_id);
        try {
            QnAForum::sendRequestToUser($em, $user_id, $project_id, $message, $user->getId());
        } catch (\Exception $ex) {
            $from = 'class: QnAForum, function: sendRequestToUser';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
        $logMessage = 'User "' . $user->getUsername() . '" sent project request to user "'
            . $contact->getUsername() . '"';
        $this->get('zectranet.projectlogger')->logEvent($logMessage, $project_id, 1);
        return new JsonResponse(1);
    }

    /**
     * @param Request $request
     * @param int $project_id
     * @return JsonResponse
     */
    public function removeUserAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $user_id = $data['user_id'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $contact = $em->find('ZectranetBundle:User', $user_id);
        try {
            QnAForum::removeUserFromProject($em, $user_id, $project_id);
        } catch (\Exception $ex) {
            $from = 'class: QnAForum, function: removeUserFromProject';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
        $logMessage = 'User "' . $user->getUsername() . '" remove user "'
            . $contact->getUsername() . '" from project';
        $this->get('zectranet.projectlogger')->logEvent($logMessage, $project_id, 1);
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
        try {
            /** @var Req $request */
            $request = QnAForum::removeRequest($em, $request_id);
            if ($request) {
                $logMessage = 'User "' . $user->getUsername() . '" remove user "'
                    . $request->getUser()->getUsername() . '" from request grid';
                $this->get('zectranet.projectlogger')->logEvent($logMessage, $project_id, 1);
                return new JsonResponse(1);
            } else {
                return new JsonResponse(0);
            }
        } catch (\Exception $ex) {
            $from = 'class: HFForum, function: removeRequest';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
    }
}