<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ZectranetBundle\Entity\QnAPost;
use ZectranetBundle\Entity\QnAThread;
use ZectranetBundle\Entity\User;

class QnAForumController extends Controller {

    /**
     * @Security("has_role('ROLE_USER')")
     * @param $project_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showForumAction($project_id)
    {
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:QnAForum')->find($project_id);
        return $this->render('ZectranetBundle::QnAForum.html.twig', array('forum' => $project));
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
        return $this->render('ZectranetBundle::QnAThread.html.twig', array('forum' => $project, 'thread' => $thread));
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
}