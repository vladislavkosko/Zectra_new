<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\ProjectPost;
use ZectranetBundle\Entity\User;

class ProjectController extends Controller
{

    /**
     * @param int $project_id
     * @return Response
     */
    public function indexAction($project_id)
    {
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')
            ->find($project_id);
        return $this->render('@Zectranet/project.html.twig', array('project' => $project));
    }

    /**
     * @Route("/project/add")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return Response
     */
    public function addProjectAction(Request $request) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $name = $request->request->get('project_name');
        $description = $request->request->get('project_description');

        /** @var Project $project */
        $project = Project::addNewProject($em, $this->getUser(), $name, $description);
        return $this->redirectToRoute('zectranet_show_project', array('project_id' => $project->getId()));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     */
    public function deleteAction(Request $request, $project_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var AuthorizationChecker $auth_checker */
        $auth_checker = $this->get('security.authorization_checker');

        /** @var Project $project */
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);

        /** @var User $user */
        $user = $this->getUser();

        if ($project && ($project->getOwnerid() == $user->getId() || $auth_checker->isGranted('ROLE_ADMIN'))) {
            Project::deleteProject($em, $project_id);
        }

        return $this->redirectToRoute('zectranet_user_page');
    }

    /**
     * @Route("/project/{project_id}/addNewPost")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $project_id
     * @return RedirectResponse
     */
    public function addPostAction(Request $request, $project_id) {
        $message = $request->request->get('message');
        if ($request->getMethod() === "POST" && $message !== '') {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $user_id = $this->getUser()->getId();
            ProjectPost::addNewPost($em, $user_id, $project_id, $message);
        }
        return $this->redirectToRoute('zectranet_show_project', array('project_id' => $project_id));
    }
}