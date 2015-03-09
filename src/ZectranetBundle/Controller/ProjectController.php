<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\ProjectPost;
use ZectranetBundle\Entity\Task;
use ZectranetBundle\Entity\User;

class ProjectController extends Controller
{
    /**
     * @Route("/project/{project_id}")
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function indexAction($project_id)
    {
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);
        $task_priority = $this->getDoctrine()->getRepository('ZectranetBundle:TaskPriority')->findAll();
        $task_types = $this->getDoctrine()->getRepository('ZectranetBundle:TaskType')->findAll();
        return $this->render('@Zectranet/project.html.twig', array(
            'project' => $project,
            'task_priority' => $task_priority,
            'task_types' => $task_types
            )
        );
    }

    /**
     * @Route("/project/{project_id}/settings")
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function settingsAction($project_id)
    {
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);
        $users = $this->getDoctrine()->getRepository('ZectranetBundle:User')->findAll();
        return $this->render('@Zectranet/projectSettings.html.twig', array(
            'project' => $project,
            'users' => $users,
        ));
    }

    /**
     * @Route("/project/{project_id}/getMembers")
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function getMembersAction($project_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $jsonProjectUsers = Project::getJsonProjectMembers($em, $project_id);
        $jsonNotProjectUsers = Project::getJsonNotProjectMembers($em, $project_id);

        $response = new Response(json_encode(array(
            'projectMembers' => $jsonProjectUsers,
            'users' => $jsonNotProjectUsers
        )));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/project/{project_id}/saveMembersState")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @return Response
     */
    public function saveMembersAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $ids = array();
        foreach ($data['users'] as $user) {
            $user = (object) $user;
            $ids[] = $user->id;
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $users = $em->getRepository('ZectranetBundle:User')->findBy(array('id' => $ids));
        $project->setUsers($users);

        $em->persist($project);
        $em->flush();

        $response = new Response(json_encode(array('success' => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/project/{project_id}/getOffices")
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function getOfficesAction($project_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $jsonProjectOffices = Project::getJsonProjectOffices($em, $project_id);
        $jsonNotProjectOffices = Project::getJsonNotProjectOffices($em, $project_id);

        $response = new Response(json_encode(array(
            'projectOffices' => $jsonProjectOffices,
            'offices' => $jsonNotProjectOffices
        )));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/project/{project_id}/addOffices")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @return Response
     */
    public function addOfficesAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $ids = array();
        foreach ($data['offices'] as $office) {
            $office = (object) $office;
            $ids[] = $office->id;
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        Project::addOfficesToProject($em, $ids, $project_id);
        $response = new Response(json_encode(array('success' => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/project/{project_id}/removeOffices")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @return Response
     */
    public function removeOfficesAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $ids = array();
        foreach ($data['offices'] as $office) {
            $office = (object) $office;
            $ids[] = $office->id;
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        Project::removeOfficesFromProject($em, $ids, $project_id);
        $response = new Response(json_encode(array('success' => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
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
     * @Route("/project/{project_id}/delete")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @return RedirectResponse
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
     * @Route("/project/{project_id}/deleteEpicStories")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @return RedirectResponse
     */
    public function deleteEpicStoriesAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        /** @var array $ids */
        $ids = $data['epicStories'];

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var AuthorizationChecker $auth_checker */
        $auth_checker = $this->get('security.authorization_checker');

        /** @var array of Project $epicStories */
        $epicStories = $em->getRepository('ZectranetBundle:Project')->findBy(array('id' => $ids));

        /** @var User $user */
        $user = $this->getUser();

        if (count($epicStories) > 0) {
            foreach ($epicStories as $story) {
                if ($story->getOwnerid() == $user->getId() || $auth_checker->isGranted('ROLE_ADMIN')) {
                    Project::deleteProject($em, $story->getId());
                }
            }
        }

        $response = new Response(json_encode(array('success' => 'success')));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
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

    /**
     * @Route("/project/{project_id}/addNewTask")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $project_id
     * @return RedirectResponse
     */
    public function addTaskAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $data = (object) $data['task'];

        $parameters = array(
            'name' => $data->Name,
            'description' => $data->Description,
            'type' => $data->Type,
            'priority' => $data->Priority,
            'startdate' => date('Y-m-d', strtotime($data->StartDate)),
            'enddate' => date('Y-m-d', strtotime($data->EndDate)),
        );

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);

        $task = Task::addNewTask($em, $user, $project_id, $parameters);

        $nameEpicStory = null;
        if ($project->getParent())
        {
            $nameEpicStory = $project->getName();
            $project = $project->getParent();
        }
        $this->get('zectranet.notifier')->createNotification("task_added", $project, $user, $project, $nameEpicStory);

        $response = new Response(json_encode(array('Tasks' => $task->getInArray())));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/project/{project_id}/addNewSubTask")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $project_id
     * @return Response
     */
    public function addSubTaskAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $data = (object) $data['task'];
        $parameters = array(
            'name' => $data->name,
            'description' => $data->description,
            'type' => $data->type,
            'priority' => $data->priority,
            'startdate' => date('Y-m-d', strtotime($data->startdate)),
            'enddate' => date('Y-m-d', strtotime($data->enddate)),
            'parent' => $data->parent
        );

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);

        $task = Task::addNewSubTask($em, $user, $project_id, $parameters);

        $nameEpicStory = null;
        if ($project->getParent())
        {
            $nameEpicStory = $project->getName();
            $project = $project->getParent();
        }
        $this->get('zectranet.notifier')->createNotification("task_added", $project, $user, $project, $nameEpicStory);


        $response = new Response(json_encode(array('Tasks' => $task->getInArray())));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/project/{project_id}/getEpicStories")
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function getEpicStoriesAction($project_id) {
        $epicStories = $this->getDoctrine()->getRepository('ZectranetBundle:Project')
            ->findBy(array('parentid' => $project_id));
        $jsonEpicStories = array();
        /** @var Project $epicStory */
        foreach ($epicStories as $epicStory) {
            $jsonEpicStories[] = $epicStory->getInArray();
        }

        $response = new Response(json_encode(array('EpicStories' => $jsonEpicStories)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/project/{project_id}/addNewEpicStory")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @return Response
     */
    public function addNewEpicStoryAction(Request $request, $project_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);

        $data = json_decode($request->getContent(), true);
        $data = (object) $data['story'];

        $epicStory = Project::addEpicStory($em, $project_id, $user, $data);

        $this->get('zectranet.notifier')->createNotification("epic_story_added", $project, $user, $project);

        $response = new Response(json_encode(array('EpicStory' => $epicStory->getInArray())));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}