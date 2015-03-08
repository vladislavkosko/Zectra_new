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
}