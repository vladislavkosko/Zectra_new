<?php
namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ZectranetBundle\Entity\EntityOperations;
use ZectranetBundle\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PublicMarketController extends Controller {
    /**
     * @Route("/public_market")
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function indexAction() {
        $projects = $this->getDoctrine()->getRepository('ZectranetBundle:Project')
            ->findBy(array('parentid' => null, 'visible' => true));
        $offices = $this->getDoctrine()->getRepository('ZectranetBundle:Office')
            ->findBy(array('visible' => true));

        return $this->render('@Zectranet/public-market.html.twig', array(
            'projects' => $projects,
            'offices' => $offices
        ));
    }

    /**
     * @Route("/public_market/all_contacts")
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function getAllContactsAction() {
        $allMembers = $this->getDoctrine()->getRepository('ZectranetBundle:User')->findAll();
        return $this->render('@Zectranet/publicMarketAllMembers.html.twig', array(
            'users' => $allMembers
        ));
    }
}