<?php
namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function editMainInfoAction ($task_id) {
        $task = $this->getDoctrine()->getRepository('ZectranetBundle:Task')->find($task_id);
        return $this->redirectToRoute('zectranet_task_show', array('task_id' => $task_id));
    }

    public function editDetailInfoAction ($task_id) {
        $task = $this->getDoctrine()->getRepository('ZectranetBundle:Task')->find($task_id);
        return $this->redirectToRoute('zectranet_task_show', array('task_id' => $task_id));
    }

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