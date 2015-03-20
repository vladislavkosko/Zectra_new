<?php

namespace ZectranetBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use ZectranetBundle\Entity\EntityOperations;
use ZectranetBundle\Entity\Notification;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\ProjectPost;
use ZectranetBundle\Entity\RequestType;
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
        /** @var User $user */
        $user = $this->getUser();

        $isUserInProject = false;
        if (!$user->getProjects()->contains($project)
            && !$user->getOwnedProjects()->contains($project)) {
            /** @var Office $office */
            foreach ($user->getAssignedOffices() as $office) {
                if ($office->getProjects()->contains($project)) {
                    $isUserInProject = true; break;
                }
            }

            /** @var Office $office */
            foreach ($user->getOwnedOffices() as $office) {
                if ($office->getProjects()->contains($project)) {
                    $isUserInProject = true; break;
                }
            }

            if (!$isUserInProject) {
                return $this->redirectToRoute('zectranet_user_home');
            }
        }

        $this->get('zectranet.notifier')->clearNotificationsByProjectId($project_id);

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
        return $this->render('@Zectranet/projectSettings.html.twig', array(
            'project' => $project,
        ));
    }

    /**
     * @Route("/project/{project_id}/settings/visibleStateChange")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @return Response
     */
    public function visibleStateChangeAction(Request $request, $project_id)
    {
        $data = json_decode($request->getContent(), true);
        $data = (object)$data;
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $project->setVisible($data->visible);
        $em->persist($project);
        $em->flush();

        $response = new Response(json_encode(array('success' => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
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
        /** @var Project $project */
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        $users = $em->getRepository('ZectranetBundle:User')->findBy(array('id' => $ids));

        if ($data['status'] == 1)
        {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            /** @var RequestType $type */
            $type = $this->getDoctrine()->getRepository('ZectranetBundle:RequestType')->find(2);

            $usersNames = array();
            foreach ($project->getUsers() as $user)
                $usersNames[] = $user->getUsername();

            $usersRequest = array();
            foreach ($users as $user)
            {
                if (!in_array($user->getUsername(), $usersNames))
                    $usersRequest[] = $user;
            }

            foreach ($usersRequest as $user)
                \ZectranetBundle\Entity\Request::addNewRequest($em, $user, $type, $project);

            /** @var User $user */
            $user = $this->getUser();
            $this->get('zectranet.notifier')->createNotification("request_user_project", $user, $usersRequest, $project);
        }

        if ($data['status'] == 0)
        {
            $project->setUsers($users);
            $em->persist($project);
            $em->flush();
        }

        $response = new Response(json_encode(array('success' => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/project/{project_id}/acceptRequestUserProject")
     * @Security("has_role('ROLE_USER')")
     * @param $project_id
     * @return RedirectResponse
     */
    public function acceptRequestUserProjectAction($project_id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        /** @var Project $project */
        $project = $em->getRepository('ZectranetBundle:Project')->find($project_id);
        /** @var Request $request */
        $request = $em->getRepository('ZectranetBundle:Request')->findOneBy(array('userid' => $user->getId(), 'projectid' => $project_id, 'typeid' => 2));
        /** @var Notification $notification */
        $notification = $em->getRepository('ZectranetBundle:Notification')->findOneBy(array('userid' => $user->getId(), 'destinationid' => $project_id, 'type' => 'request_user_project'));

        $usersProject = $project->getUsers();
        $usersProject[] = $user;

        $project->setUsers($usersProject);
        $em->persist($project);
        $em->remove($request);
        $em->remove($notification);
        $em->flush();

        return $this->redirectToRoute('zectranet_show_project', array('project_id' => $project_id));
    }

    /**
     * @Route("/project/{project_id}/{office_id}/acceptRequestOfficeProject")
     * @Security("has_role('ROLE_USER')")
     * @param $project_id
     * @param $office_id
     * @return RedirectResponse
     */
    public function acceptRequestOfficeProjectAction($project_id, $office_id)
    {
       /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        Project::addOfficeToProject($em, $office_id, $project_id);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();

        /** @var Request $request */
        $requestOffice = $em->getRepository('ZectranetBundle:Request')->findOneBy(array('userid' => $user->getId(), 'projectid' => $project_id, 'typeid' => 3));
        /** @var Notification $notification */
        $notification = $em->getRepository('ZectranetBundle:Notification')->findOneBy(array('userid' => $user->getId(), 'destinationid' => $project_id, 'type' => 'request_project'));

        $em->remove($requestOffice);
        if ($notification != null)
            $em->remove($notification);
        $em->flush();

        return $this->redirectToRoute('zectranet_show_project', array('project_id' => $project_id));
    }

    /**
     * @Route("/project/{project_id}/declineRequestUserProject")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $project_id
     * @return RedirectResponse
     */
    public function declineRequestUserProjectAction(Request $request, $project_id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();

        /** @var Request $request */
        $requestUser = $em->getRepository('ZectranetBundle:Request')->findOneBy(array('userid' => $user->getId(), 'projectid' => $project_id, 'typeid' => 2));
        /** @var Notification $notification */
        $notification = $em->getRepository('ZectranetBundle:Notification')->findOneBy(array('userid' => $user->getId(), 'destinationid' => $project_id, 'type' => 'request_user_project'));

        $em->remove($requestUser);
        if ($notification != null)
            $em->remove($notification);
        $em->flush();

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * @Route("/project/{project_id}/declineRequestOfficeProject")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $project_id
     * @return RedirectResponse
     */
    public function declineRequestOfficeProjectAction(Request $request, $project_id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();

        /** @var Request $request */
        $requestOffice = $em->getRepository('ZectranetBundle:Request')->findOneBy(array('userid' => $user->getId(), 'projectid' => $project_id, 'typeid' => 3));
        /** @var Notification $notification */
        $notification = $em->getRepository('ZectranetBundle:Notification')->findOneBy(array('userid' => $user->getId(), 'destinationid' => $project_id, 'type' => 'request_project'));

        $em->remove($requestOffice);
        if ($notification != null)
            $em->remove($notification);
        $em->flush();

        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
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
        foreach ($data['offices'] as $office)
        {
            if ((!isset($office['request'])) or ((isset($office['request'])) and ($office['request'] == 2)))
            {
                $office = (object) $office;
                $ids[] = $office->id;
            }
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var RequestType $type */
        $type = $this->getDoctrine()->getRepository('ZectranetBundle:RequestType')->find(3);
        /** @var Project $project */
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);
        /** @var User $user */
        $user = $this->getUser();

        $offices = $this->getDoctrine()->getRepository('ZectranetBundle:Office')->findBy(array('id' => $ids));

        foreach ($offices as $office)
        {
            if ($user->getId() != $office->getOwner()->getId())
            {
                \ZectranetBundle\Entity\Request::addNewRequest($em, $office->getOwner(), $type, $project, $office);
                $this->get('zectranet.notifier')->createNotification("request_project", $user, $office->getOwner(), $project, $office);
            }
            else
                Project::addOfficeToProject($em, $office->getId(), $project_id);
        }

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
            $this->get('zectranet.notifier')->clearAllNotificationsByProjectId($project_id);
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
                    $this->get('zectranet.notifier')->createNotification("epic_story_deleted", $story->getParent(), $user, $story->getParent(), null, null, $story->getName());
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
            'name' => $data->name,
            'description' => $data->description,
            'type' => $data->type,
            'priority' => $data->priority,
            'startdate' => date('Y-m-d', strtotime($data->startdate)),
            'enddate' => date('Y-m-d', strtotime($data->enddate)),
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
        $this->get('zectranet.notifier')->createNotification("task_added", $project, $user, $project, $nameEpicStory, null, $parameters['name']);

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
        $this->get('zectranet.notifier')->createNotification("task_added", $project, $user, $project, $nameEpicStory, null, $parameters['name']);


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

        $this->get('zectranet.notifier')->createNotification("epic_story_added", $project, $user, $project, null, null, $data->name);

        $response = new Response(json_encode(array('EpicStory' => $epicStory->getInArray())));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/project/{project_id}/getVersions")
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function getProjectVersionsAction($project_id) {
        /** @var Project $project */
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);
        /** @var ArrayCollection $versions */
        $versions = EntityOperations::arrayToJsonArray($project->getVersions());
        $response = new Response(json_encode(array('versions' => $versions)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/project/{project_id}/addNewVersion")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @return Response
     */
    public function addNewProjectVersionAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $version = (object) $data['version'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        Project::addNewProjectVersion($em, $project_id, $user->getId(), $version);
        $response = new Response(json_encode(array('success' => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/project/{project_id}/showVersions")
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function showProjectVersionAction($project_id) {
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);
        return $this->render('@Zectranet/projectVersions.html.twig', array('project' => $project));

    }
}