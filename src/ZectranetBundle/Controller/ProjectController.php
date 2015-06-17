<?php

namespace ZectranetBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use ZectranetBundle\Entity\EntityOperations;
use ZectranetBundle\Entity\HFForum;
use ZectranetBundle\Entity\Notification;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\ProjectPermissions;
use ZectranetBundle\Entity\ProjectPost;
use ZectranetBundle\Entity\QnAForum;
use ZectranetBundle\Entity\RequestType;
use ZectranetBundle\Entity\Task;
use ZectranetBundle\Entity\User;
use ZectranetBundle\Entity\Request as Req;

class ProjectController extends Controller
{
    /**
     * @Route("project/{project_id}")
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function indexAction($project_id)
    {
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getProjects()->contains($project)
            && !$user->getOwnedProjects()->contains($project)
            || $project->getArchived())
        {
            return $this->redirectToRoute('zectranet_show_office', array('office_id' => $user->getHomeOfficeID()));
        }

        $this->get('zectranet.notifier')->clearNotificationsByProjectId($project_id);

        $task_priority = $this->getDoctrine()
            ->getRepository('ZectranetBundle:TaskPriority')->findAll();
        $task_types = $this->getDoctrine()
            ->getRepository('ZectranetBundle:TaskType')->findAll();

        return $this->render('@Zectranet/project.html.twig', array(
            'project' => $project,
            'task_priority' => $task_priority,
            'task_types' => $task_types,
            'office' => $project->getOffice()
        ));
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

        $response = new JsonResponse(array('success' => true));
        return $response;
    }

    /**
     * @Route("/project/add")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $office_id
     * @return Response
     */
    public function addProjectAction(Request $request, $office_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $name = $request->request->get('name');
        $type = $request->request->get('type');

        /** @var User $user */
        $user = $this->getUser();

        $params = array(
            'name' => $name,
            'type' => $type,
        );

        switch ($type) {
            case 1:
                $project = null;
                try{
                    $project = QnAForum::addNewQnAForum($em, $user, $office_id, $name);
                    return $this->redirectToRoute('zectranet_show_QnA_forum',
                        array('project_id' => $project->getId()));
                } catch (\Exception $ex) {
                    $from = "class: QnAForum, function: addNewQnAForum";
                    $this->get('zectranet.errorlogger')->registerException($ex, $from);
                }
                break;
            case 2:
                $project = null;
                try {
                    /** @var HFForum $project */
                    $project = HFForum::addNewHeaderForum($em, $user->getId(), $office_id, $params);
                    return $this->redirectToRoute('zectranet_show_header_forum',
                        array('project_id' => $project->getId()));
                } catch (\Exception $ex) {
                    $from = "class: HFForum, function: addNewHeaderForum";
                    $this->get('zectranet.errorlogger')->registerException($ex, $from);
                }
                break;
            case 3: break;
            case 4:
                $project = null;
                try {
                    $project = Project::addNewProject($em, $user, $name, $type, $office_id);
                    ProjectPermissions::addPermission($em, $project, $user);
                    return $this->redirectToRoute('zectranet_show_project',
                        array('project_id' => $project->getId()));
                } catch (\Exception $ex) {
                    $from = "class: Project, function: addNewProject";
                    $this->get('zectranet.errorlogger')->registerException($ex, $from);
                }
                break;
            default: break;
        }
        return $this->redirectToRoute('zectranet_show_office', array('office_id' => $office_id));
    }

    public function savePermissionsAction(Request $request, $project_id, $user_id)
    {
        /** @var Sprint $sprint */
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);

        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('ZectranetBundle:User')->find($user_id);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $enableCreateSprint = $request->request->get('enableCreateSprint');

        ProjectPermissions::savePermission($em, $project, $user, $enableCreateSprint);

        return $this->redirectToRoute('zectranet_settings_project', array('project_id' => $project_id));

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

        try {
            if ($project && ($project->getOwnerid() == $user->getId() || $auth_checker->isGranted('ROLE_ADMIN'))) {
                Project::deleteProject($em, $project_id);
                $this->get('zectranet.notifier')->clearAllNotificationsByProjectId($project_id);
            }
        } catch (\Exception $ex) {
            $from = "Class: Project, function: deleteProject";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return $this->redirectToRoute('zectranet_user_page');
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

        try {
            if (count($epicStories) > 0) {
                foreach ($epicStories as $story) {
                    if ($story->getOwnerid() == $user->getId() || $auth_checker->isGranted('ROLE_ADMIN')) {
                        Project::deleteProject($em, $story->getId());
                        $this->get('zectranet.notifier')->createNotification("epic_story_deleted", $story->getParent(), $user, $story->getParent(), null, null, $story->getName());
                    }
                }
            }
        } catch (\Exception $ex) {
            $from = "Class: Project, function: deleteProject";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
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
            try {
                ProjectPost::addNewPost($em, $user_id, $project_id, $message);
            } catch (\Exception $ex) {
                $from = "Class: ProjectPost, function: addNewPost";
                $this->get('zectranet.errorlogger')->registerException($ex, $from);
                return $this->redirectToRoute('zectranet_show_project', array('project_id' => $project_id));
            }
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

        $task = null;
        try {
            $task = Task::addNewTask($em, $user, $project_id, $parameters);
        } catch (\Exception $ex) {
            $from = "Class: Task, function: addNewTask";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

        $nameEpicStory = null;
        if ($project->getParent())
        {
            $nameEpicStory = $project->getName();
            $project = $project->getParent();
        }
        try {
            $this->get('zectranet.notifier')->createNotification("task_added", $project, $user, $project, $nameEpicStory, null, $parameters['name']);
        } catch (\Exception $ex) {
            $from = "Class: zectranet_notifier, function: createNotification";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

        return new JsonResponse($task->getInArray());
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

        $task = null;
        try {
            $task = Task::addNewSubTask($em, $user, $project_id, $parameters);
        } catch (\Exception $ex) {
            $from = "Class: Task, function: addNewSubTask";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

        $nameEpicStory = null;
        if ($project->getParent())
        {
            $nameEpicStory = $project->getName();
            $project = $project->getParent();
        }
        try {
            $this->get('zectranet.notifier')->createNotification("task_added", $project, $user, $project, $nameEpicStory, null, $parameters['name']);
        } catch (\Exception $ex) {
            $from = "Class: zectranet_notifier, function: createNotification";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

        $task = $em->find('ZectranetBundle:Task', $task->getId());

        return new JsonResponse($task->getInArray());
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

        $epicStory = null;
        try {
            $epicStory = Project::addEpicStory($em, $project_id, $user, $data, $project->getOfficeID());
        } catch (\Exception $ex) {
            $from = "Class: HFHeader, function: deleteHeader";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

        try {
            $this->get('zectranet.notifier')->createNotification("epic_story_added", $project, $user, $project, null, null, $data->name);
        } catch (\Exception $ex) {
            $from = "Class: zectranet_notifier, function: createNotification";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }

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
        try {
            Project::addNewProjectVersion($em, $project_id, $user->getId(), $version);
        } catch (\Exception $ex) {
            $from = "Class: Project, function: addNewProjectVersion";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }
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


    /**
     * @param $project_id
     * @return JsonResponse|RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getProjectSettingsInfoAction($project_id) {
        $info = array();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $project = $em->find('ZectranetBundle:Project', $project_id);
        /** @var User $user */
        $user = $this->getUser();
        if (!$project->getUsers()->contains($user)) {
            return $this->redirectToRoute('zectranet_show_office', array('office_id' => $user->getHomeOfficeID()));
        }
        $info['HO_Contacts'] = Project::getNotProjectHomeOfficeMembers($em, $user->getId(), $project_id);
        $info['All_Contacts'] = Project::getNotProjectSiteMembers($em, $project_id);
        $info['Project_Team'] = EntityOperations::arrayToJsonArray(
            $em->getRepository('ZectranetBundle:Request')->findBy(array('projectid' => $project_id))
        );

        $info['ProjectLogs'] = EntityOperations::arrayToJsonArray($project->getLogs());
        $info['timeNow'] = (new \DateTime())->format('Y-m-d H:i:s');

        return new JsonResponse($info);
    }

    /**
     * @param Request $request
     * @param $project_id
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function sendRequestAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $user_id = $data['user_id'];
        $message = $data['message'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $project = $em->find('ZectranetBundle:Project', $project_id);
        if (!$project->getUsers()->contains($user)) {
            return new JsonResponse('Not Allowed!!!');
        }
        $contact = $em->find('ZectranetBundle:User', $user_id);
        try {
            Project::sendRequestToUser($em, $user_id, $project_id, $message, $user->getId());
        } catch (\Exception $ex) {
            $from = 'class: Project, function: sendRequestToUser';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
        $logMessage = 'User "' . $user->getUsername() . '" sent project request to user "'
            . $contact->getUsername() . '"';
        $this->get('zectranet.projectlogger')->logEvent($logMessage, $project_id, 4);
        return new JsonResponse(1);
    }

    /**
     * @param Request $request
     * @param $project_id
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
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
            Project::removeRequest($em, $request_id);
            Project::sendRequestToUser($em, $user_id, $project_id, $message, $this->getUser()->getId());
        } catch (\Exception $ex) {
            $from = 'class: ProjectController, function: reSendRequestAction';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
        $logMessage = 'User "' . $user->getUsername() . '" resent project request to user "'
            . $contact->getUsername() . '"';
        $this->get('zectranet.projectlogger')->logEvent($logMessage, $project_id, 4);

        return new JsonResponse(1);
    }

    /**
     * @param $project_id
     * @param $request_id
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function deleteRequestAction($project_id, $request_id) {
        /** @var User $user */
        $user = $this->getUser();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $request = $em->find('ZectranetBundle:Request', $request_id);
        $contact = $em->find('ZectranetBundle:User', $request->getUserid());
        $project = $em->find('ZectranetBundle:Project', $project_id);
        if (!$project->getUsers()->contains($user)) {
            return new JsonResponse('Not Allowed!!!');
        }
        try {
            /** @var Req $request */
            $request = Project::removeRequest($em, $request_id);
        } catch (\Exception $ex) {
            $from = 'class: Project, function: removeRequest';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
        try {
            Project::removeUserFromProject($em, $contact->getId(), $project_id);
            $logMessage = 'User "' . $user->getUsername() . '" remove user "'
                . $request->getUser()->getUsername() . '" from request grid';
            $this->get('zectranet.projectlogger')->logEvent($logMessage, $project_id, 4);
            return new JsonResponse(1);
        } catch (\Exception $ex) {
            $from = 'class: Project, function: removeUserFromProject';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }

    }

}