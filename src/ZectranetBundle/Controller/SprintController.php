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
     * @Route("/office/{office_id}/sprint/{sprint_id}")
     * @Security("has_role('ROLE_USER')")
     * @param int $office_id
     * @param int $sprint_id
     * @return Response
     */
    public function indexAction($office_id, $sprint_id)
    {
        $office = $this->getDoctrine()->getRepository('ZectranetBundle:Office')->find($office_id);
        $sprint = $this->getDoctrine()->getRepository('ZectranetBundle:Sprint')->find($sprint_id);
        $sprint_status = $this->getDoctrine()->getRepository('ZectranetBundle:SprintStatus')->findAll();
        return $this->render('@Zectranet/sprint.html.twig', array(
                'sprint' => $sprint,
                'office' => $office,
                'sprint_status' => $sprint_status,
            )
        );
    }

    /**
     * @Route("/office/{office_id}/sprint/addSprint")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $office_id
     * @return Response
     */
    public function addSprintAction(Request $request, $office_id) {
        $sprintName = $request->request->get('name');
        $sprintDescription = $request->request->get('description');
        $params = array(
            'name' => $sprintName,
            'description' => $sprintDescription
        );

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $sprint = Sprint::addNewSprint($em, $office_id, $params);

        return $this->redirectToRoute('zectranet_show_sprint', array(
            'office_id' => $office_id,
            'sprint_id' => $sprint->getId()
        ));
    }

    /**
     * @Route("/office/{office_id}/sprint/{sprint_id}/deleteSprint")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $office_id
     * @param int $sprint_id
     * @return Response
     */
    public function deleteSprintAction(Request $request, $office_id, $sprint_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $sprint = $em->getRepository('ZectranetBundle:Sprint')->find($sprint_id);
        if ($sprint) {
            $em->remove($sprint);
            $em->flush();
        }

        return $this->redirectToRoute('zectranet_show_office', array(
            'office_id' => $office_id
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
            'office_id' => $sprint->getOfficeid(),
            'sprint_id' => $sprint->getId(),
        ));
    }
}