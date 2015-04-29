<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ZectranetBundle\Entity\EntityOperations;
use ZectranetBundle\Entity\ForgotPassword;
use ZectranetBundle\Entity\HFHeader;
use ZectranetBundle\Entity\User;

class ErrorLogController extends Controller
{
    /**
     * @Security("has_role('ROLE_WEB_DEVELOPER')")
     * @return Response
     */
    public function indexAction() {
        return $this->render('@Zectranet/errors.html.twig');
    }

    /**
     * @Security("has_role('ROLE_WEB_DEVELOPER')")
     * @return JsonResponse
     */
    public function getErrorsAction() {
        $errors = $this->getDoctrine()->getRepository('ZectranetBundle:ErrorLog')
            ->findBy(array(), array('date' => 'DESC'), 100, 0);
        return new JsonResponse(EntityOperations::arrayToJsonArray($errors));
    }
}