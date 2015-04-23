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
        Office::
        return new JsonResponse();
    }

    /**
     * @param Request $request
     * @param int $project_id
     * @return JsonResponse
     */
    public function addToArchiveAction(Request $request, $project_id) {
        return new JsonResponse();
    }

    /**
     * @param Request $request
     * @param int $project_id
     * @return JsonResponse
     */
    public function restoreFromArchiveAction(Request $request, $project_id) {
        return new JsonResponse();
    }

    /**
     * @param Request $request
     * @param int $project_id
     * @return JsonResponse
     */
    public function deleteFromArchiveAction(Request $request, $project_id) {
        return new JsonResponse();
    }
}