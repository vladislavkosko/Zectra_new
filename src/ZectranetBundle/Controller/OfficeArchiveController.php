<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use ZectranetBundle\Entity\Notification;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\OfficePost;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\HFForum;
use ZectranetBundle\Entity\QnAForum;
use ZectranetBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OfficeArchiveController extends Controller {
    /**
     * @param int $office_id
     * @return Response
     */
    public function indexAction($office_id) {
        $office = $this->getDoctrine()->getRepository('ZectranetBundle:Office')->find($office_id);
        return $this->render('@Zectranet/officeArchive.html.twig', array('office' => $office));
    }

    /**
     * @param int $office_id
     * @return JsonResponse
     */
    public function getArchiveAction($office_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $archives = Office::getOfficeArchive($em, $office_id);
        return new JsonResponse($archives);
    }

    /**
     * @param Request $request
     * @param int $office_id
     * @param int $project_id
     * @return JsonResponse
     */
    public function addToArchiveAction(Request $request, $office_id, $project_id) {
        $project_type = $request->request->get('project_type');
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        try {
            Office::addToArchive($em, $project_id, $project_type);
            return $this->redirectToRoute('zectranet_show_office', array('office_id' => $office_id));
        } catch (\Exception $ex) {
            $from = 'class: Office, function: addToArchive';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return $this->redirectToRoute('zectranet_show_office', array('office_id' => $office_id));
        }
    }

    /**
     * @param Request $request
     * @param int $project_id
     * @return JsonResponse
     */
    public function restoreFromArchiveAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $project_type = $data['project_type'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        try {
            Office::restoreFromArchive($em, $project_id, $project_type);
        } catch (\Exception $ex) {
            $from = 'class: Office, function: restoreFromArchive';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
        return new JsonResponse(1);
    }

    /**
     * @param Request $request
     * @param int $project_id
     * @return JsonResponse
     */
    public function deleteFromArchiveAction(Request $request, $project_id) {
        $data = json_decode($request->getContent(), true);
        $project_type = $data['project_type'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        try {
            Office::addToArchive($em, $project_id, $project_type);
        } catch (\Exception $ex) {
            $from = 'class: Office, function: addToArchive';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
        return new JsonResponse(1);
    }
}