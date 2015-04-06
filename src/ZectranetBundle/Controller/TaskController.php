<?php
namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\Task;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ZectranetBundle\Entity\TaskPost;
use ZectranetBundle\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZectranetBundle\Services\TaskLogger;

class TaskController extends Controller {
    /**
     * @Route("/task/{$task_id}")
     * @Security("has_role('ROLE_USER')")
     * @param int $task_id
     * @return Response
     */
    public function showTaskAction ($task_id) {
        $task = $this->getDoctrine()->getRepository('ZectranetBundle:Task')->find($task_id);
        $task_priority = $this->getDoctrine()->getRepository('ZectranetBundle:TaskPriority')->findAll();
        $task_types = $this->getDoctrine()->getRepository('ZectranetBundle:TaskType')->findAll();
        $task_statuses = $this->getDoctrine()->getRepository('ZectranetBundle:TaskStatus')->findAll();

        $this->get('zectranet.notifier')->clearNotificationsByTaskId($task_id);

        return $this->render('@Zectranet/task.html.twig', array(
            'task' => $task,
            'task_priority' => $task_priority,
            'task_types' => $task_types,
            'task_statuses' => $task_statuses
        ));
    }

    /**
     * @Route("/project/{project_id}/getTasks")
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @return Response
     */
    public function getTasksAction($project_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('ZectranetBundle:Project')
            ->find($project_id);
        $jsonTasks = Task::arrayToJson($project->getTasks());
        if (count($project->getEpicStories()) > 0) {
            /** @var Project $story */
            foreach ($project->getEpicStories() as $story) {
                $jsonTasks = array_merge($jsonTasks,
                    Task::arrayToJson($story->getTasks()));
            }
        }

        $response = new Response(json_encode(array('Tasks' => $jsonTasks)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/task/{task_id}/deleteTask")
     * @Security("has_role('ROLE_USER')")
     * @param int $task_id
     * @return Response
     */
    public function deleteTaskAction($task_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $task = $this->getDoctrine()->getRepository('ZectranetBundle:Task')->find($task_id);
        $project = $task->getProject();

        Task::deleteTask($em, $task_id);

        $nameEpicStory = null;
        if ($project->getParent())
        {
            $nameEpicStory = $project->getName();
            $project = $project->getParent();
        }
        $this->get('zectranet.notifier')->clearAllNotificationsByTaskId($task_id);

        $this->get('zectranet.notifier')->createNotification("task_deleted", $project, $user, $project, $nameEpicStory, null, $task->getName());

        $response = new Response(json_encode(null));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param int $task_id
     * @return Request
     */
    public function getSingleTaskAction ($task_id) {
        $task = $this->getDoctrine()->getRepository('ZectranetBundle:Task')->find($task_id);
        $response = new Response(json_encode(array('task' => $task->getInArray())));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @param int $task_id
     * @return RedirectResponse
     */
    public function editInfoAction (Request $request, $task_id) {
        $data = json_decode($request->getContent(), true);
        $data = (object) $data['task'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var TaskLogger $logger */
        $logger = $this->get('zectranet.tasklogger');

        $parameters = array(
            'name' => $data->name,
            'type' => $data->type['id'],
            'priority' => $data->priority['id'],
            'status' => $data->status['id'],
            'project' => $data->projectid,
            'assigned' => $data->assigned,
            'progress' => $data->progress,
            'estimated_hours' => $data->estimatedHours,
            'estimated_minutes' => $data->estimatedMinutes,
            'start_date' => date('Y-m-d', strtotime($data->startDate)),
            'end_date' => date('Y-m-d', strtotime($data->endDate)),
            'version' => $data->versionid,
        );

        $task = Task::editInfo($em, $logger, $task_id, $parameters);

        $response = new Response(json_encode(array('task' => $task)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @param int $task_id
     * @return RedirectResponse
     */
    public function editDescriptionAction (Request $request, $task_id) {
        $task = $this->getDoctrine()->getRepository('ZectranetBundle:Task')->find($task_id);
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var TaskLogger $logger */
        $logger = $this->get('zectranet.tasklogger');
        $description = $request->request->get('description');
        $task = Task::editTaskDescription($em, $logger, $task_id, $description);
        return $this->redirectToRoute('zectranet_task_show', array('task_id' => $task_id));
    }

    public function getPostsAction(Request $request, $task_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);
        $data = (object) $data;

        $phpPosts = TaskPost::getPostsOffset($em,$task_id, $data->offset, $data->count);
        $jsonPosts = array();
        /** @var TaskPost $post */
        foreach ($phpPosts as $post) {
            $jsonPosts[] = $post->getInArray();
        }

        $response = new Response(json_encode(array('Posts' => $jsonPosts)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    public function addPostAction(Request $request, $task_id)
    {
        $post = json_decode($request->getContent(), true);
        $post = (object)$post;

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $task = $this->getDoctrine()->getRepository('ZectranetBundle:Task')->find($task_id);

        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($task->getProjectid());
        $nameEpicStory = null;
        if ($project->getParent())
        {
            $nameEpicStory = $project->getName();
            $project = $project->getParent();
        }

        $new_post = TaskPost::addNewPost($em, $user->getId(), $task_id, $post->message);

        $usersName = array();
        $privateForAll = false;
        if ($post->usersForPrivateMessage == 'all')
        {
            $this->get('zectranet.notifier')->createNotification("private_message_task", $user, $user, $project, $nameEpicStory, $post, null, null, $task);
            $privateForAll = true;
        }

        if (($post->usersForPrivateMessage != null) and ($privateForAll == false))
        {
            $usersProjectNames = array();
            $usersProjectNames[] = $project->getOwner()->getUsername();
            foreach ($project->getUsers() as $user)
                $usersProjectNames[] = $user->getUsername();

            foreach ($project->getOffices() as $office)
            {
                $usersProjectNames[] = $office->getOwner()->getUsername();
                foreach ($office->getUsers() as $user)
                    $usersProjectNames[] = $user->getUsername();
            }

            foreach ($post->usersForPrivateMessage as $userName)
                if (in_array($userName, $usersProjectNames))
                    $usersName[] = $userName;

            if (count($usersName) > 0)
            {
                $usersEmail = $this->getDoctrine()->getRepository('ZectranetBundle:User')->findBy(array('username' => $usersName));
                $user = $this->getUser();
                $this->get('zectranet.notifier')->createNotification("private_message_task", $user, $user, $project, $nameEpicStory, $post, $usersEmail, null, $task);
            }
        }

        $user = $this->getUser();
        if ($privateForAll == false)
            $this->get('zectranet.notifier')->createNotification("message_task", $user, $user, $project, $nameEpicStory, $post, $usersName, null, $task);


        $response = new Response(json_encode(array('newPost' => $new_post->getInArray())));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}