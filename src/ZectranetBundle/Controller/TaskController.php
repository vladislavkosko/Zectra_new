<?php
namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use ZectranetBundle\Entity\Task;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
        $tasks = $this->getDoctrine()->getRepository('ZectranetBundle:Task')
            ->findBy(array('projectid' => $project_id), array('id' => 'DESC'));
        $jsonTasks = array();
        /** @var Task $task */
        foreach($tasks as $task) {
            $jsonTasks[] = $task->getInArray();
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

        Task::deleteTask($em, $task_id);

        $response = new Response(json_encode(null));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param Request $request
     * @param int $task_id
     * @return RedirectResponse
     */
    public function editMainInfoAction (Request $request, $task_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        /** @var Task $task */
        $task = $this->getDoctrine()->getRepository('ZectranetBundle:Task')->find($task_id);
        /** @var TaskLogger $logger */
        $logger = $this->get('zectranet.tasklogger');

        $parameters = array(
            'name' => $request->request->get('name'),
            'type' => $request->request->get('type'),
            'priority' => $request->request->get('priority'),
            'status' => $request->request->get('status'),
            'project' => $request->request->get('project'),
        );

        $task = Task::editMainInfo($em, $logger, $task_id, $parameters);
        return $this->redirectToRoute('zectranet_task_show', array('task_id' => $task_id));
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
}