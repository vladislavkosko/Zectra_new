<?php

namespace ZectranetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class QnAForumController extends Controller {

    public function showForumAction($project_id)
    {
        $forum = $this->getDoctrine()->getRepository('ZectranetBundle:QnAForum')->find($project_id);
        return $this->render('ZectranetBundle::QnAForum.html.twig', array('forum' => $forum));
    }
}