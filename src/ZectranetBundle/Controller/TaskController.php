<?php
namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ZectranetBundle\Entity\Task;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ZectranetBundle\Entity\User;

class TaskController extends Controller {
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
}