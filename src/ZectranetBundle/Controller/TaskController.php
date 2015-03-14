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
    public function editMainInfoAction (Request $request, $task_id) {
        $data = json_decode($request->getContent(), true);
        $data = (object) $data['task'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        /** @var Task $task */
        $task = $this->getDoctrine()->getRepository('ZectranetBundle:Task')->find($task_id);
        /** @var TaskLogger $logger */
        $logger = $this->get('zectranet.tasklogger');

        $parameters = array(
            'name' => $data->name,
            'type' => $data->type['id'],
            'priority' => $data->priority['id'],
            'status' => $data->status['id'],
            'project' => $data->project['id'],
        );

        Task::editMainInfo($em, $logger, $task_id, $parameters);

        $response = new Response(json_encode(array('success' => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    /**
     * @param Request $request
     * @param int $task_id
     * @return RedirectResponse
     */
    public function editDetailInfoAction (Request $request, $task_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        /** @var Task $task */
        $task = $this->getDoctrine()->getRepository('ZectranetBundle:Task')->find($task_id);
        /** @var TaskLogger $logger */
        $logger = $this->get('zectranet.tasklogger');

        $parameters = array(
            'assigned' => $request->request->get('assigned'),
            'progress' => $request->request->get('progress'),
            'estimated_hours' => $request->request->get('estimated_hours'),
            'estimated_minutes' => $request->request->get('estimated_minutes'),
            'start_date' => $request->request->get('start_date'),
            'end_date' => $request->request->get('end_date'),
        );

        $task = Task::editDetailsInfo($em, $logger, $task_id, $parameters);

        return $this->redirectToRoute('zectranet_task_show', array('task_id' => $task_id));
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

        $new_post = TaskPost::addNewPost($em, $user->getId(), $task_id, $post->message);

        $usersName = array();
        $privateForAll = false;
        /*if ($post->usersForPrivateMessage == 'all')
        {
            $this->get('zectranet.notifier')->createNotification("private_message_office", $user, $user, $task, null, $post);
            $privateForAll = true;
        }

        if (($post->usersForPrivateMessage != null) and ($privateForAll == false))
        {
            $usersEmail = $this->getDoctrine()->getRepository('ZectranetBundle:User')->findBy(array('username' => $post->usersForPrivateMessage));
            if (count($usersEmail) > 0)
            {
                foreach ($usersEmail as $userEmail)
                    $usersName[] = $userEmail->getUsername();
                $this->get('zectranet.notifier')->createNotification("private_message_office", $user, $user, $task, null, $post, $usersEmail);
            }

        }

        if ($privateForAll == false)
            $this->get('zectranet.notifier')->createNotification("message_office", $user, $user, $task, null, $post, $usersName);
        */

        $response = new Response(json_encode(array('newPost' => $new_post->getInArray())));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}