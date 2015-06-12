<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\Task;
use ZectranetBundle\Entity\User;
use ZectranetBundle\Entity\Sprint;

class SprintController extends Controller {
    /**
     * @Route("/project/{project_id}/sprint/{sprint_id}")
     * @Security("has_role('ROLE_USER')")
     * @param int $project_id
     * @param int $sprint_id
     * @return Response
     */
    public function indexAction($project_id, $sprint_id)
    {
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);
        $sprint = $this->getDoctrine()->getRepository('ZectranetBundle:Sprint')->find($sprint_id);
        $sprint_status = $this->getDoctrine()->getRepository('ZectranetBundle:SprintStatus')->findAll();
        return $this->render('@Zectranet/sprint.html.twig', array(
                'sprint' => $sprint,
                'project' => $project,
                'sprint_status' => $sprint_status,
            )
        );
    }

    /**
     * @Route("/sprint/{project_id}/addSprint")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @return Response
     */
    public function addSprintAction(Request $request, $project_id) {
        $sprintName = $request->request->get('name');
        $sprintDescription = $request->request->get('description');
        $params = array(
            'name' => $sprintName,
            'description' => $sprintDescription
        );

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $sprint = Sprint::addNewSprint($em, $project_id, $params);

        return $this->redirectToRoute('zectranet_show_sprint', array(
            'project_id' => $project_id,
            'sprint_id' => $sprint->getId()
        ));
    }

    /**
     * @Route("/project/{project_id}/sprint/{sprint_id}/deleteSprint")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $project_id
     * @param int $sprint_id
     * @return Response
     */
    public function deleteSprintAction(Request $request, $project_id, $sprint_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Sprint $sprint */
        $sprint = $em->getRepository('ZectranetBundle:Sprint')->find($sprint_id);
        if ($sprint) {
            /** @var Task $task */
            foreach ($sprint->getTasks() as $task)
            {
                $task->setSprintid(null);
                $task->setSprint(null);
            }
            $em->remove($sprint);
            $em->flush();
        }

        return $this->redirectToRoute('zectranet_show_project', array(
            'project_id' => $project_id
        ));
    }

    /**
     * @Route("/sprint/{sprint_id}/addTasksToSprint")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $sprint_id
     * @return Response
     */
    public function addTasksToSprintAction(Request $request, $sprint_id) {
        $data = json_decode($request->getContent(), true);
        $tasks = (object) $data['tasks'];
        $ids = array();
        foreach ($tasks as $task) {
            $ids[] = $task['id'];
        }
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $tasks = Sprint::addTasksToSprint($em, $sprint_id, $ids);

        return new JsonResponse($tasks);
    }

    /**
     * @Route("/sprint/{sprint_id}/getTasks")
     * @Security("has_role('ROLE_USER')")
     * @param $sprint_id
     * @return Response
     */
    public function getTasksAction($sprint_id) {
        /** @var Task $tasks */
        $tasks = $this->getDoctrine()->getRepository('ZectranetBundle:Task')
            ->findBy(array('sprintid' => $sprint_id), array('id' => 'DESC'));
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
     * @Route("/sprint/{sprint_id}/changeState/{state}")
     * @Security("has_role('ROLE_USER')")
     * @param int $sprint_id
     * @param int $state
     * @return Response
     */
    public function changeSprintStateAction($sprint_id, $state) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $sprint = $em->getRepository('ZectranetBundle:Sprint')->find($sprint_id);
        $sprint->setStatus($em->getRepository('ZectranetBundle:SprintStatus')->find($state));
        $em->persist($sprint);
        $em->flush();

        return $this->redirectToRoute('zectranet_show_sprint', array(
            'project_id' => $sprint->getProjectid(),
            'sprint_id' => $sprint->getId(),
        ));
    }
}