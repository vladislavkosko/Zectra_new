<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ZectranetBundle\Entity\OfficeProfile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ZectranetBundle\Entity\User;
use ZectranetBundle\Entity\Office;

class OfficeProfileController extends Controller
{

        public function indexAction(Request $request, $office_id)
        {
            $office = $this->getDoctrine()->getRepository('ZectranetBundle:Office')->find($office_id);
            return $this->render('@Zectranet/officeProfile.html.twig', array('office'=>$office));
        }

        public function indexEditAction(Request $request, $office_id)
        {
            $office = $this->getDoctrine()->getRepository('ZectranetBundle:Office')->find($office_id);
            return $this->render('@Zectranet/officeEditProfile.html.twig', array('office'=>$office));
        }
}