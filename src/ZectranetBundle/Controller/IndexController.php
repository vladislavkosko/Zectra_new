<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZectranetBundle\Entity\User;

class IndexController extends Controller
{
    /**
     * @Route("/", name="zectranet_homepage")
     */
    public function indexAction()
    {
        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(20000);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $this->render('@Zectranet/homepage.html.twig', array(), $response);
    }

    /**
     * @Route("/login", name="zectranet_login")
     * @return Response
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@Zectranet/login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }

    /**
     * @Route("/login_check", name="zectranet_login_check")
     */
    public function loginCheckAction() { }

    /**
     * @Route("/signup", name="zectranet_signup")
     */
    public function signupAction() {
        return $this->render('@Zectranet/register.html.twig');
    }

    /**
     * @Route("/signup", name="zectranet_signup_action")
     * @param Request $request
     * @return Response
     */
    public function signupActAction(Request $request) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $parameters = array(
            'name' => $request->request->get('name'),
            'surname' => $request->request->get('surname'),
            'email' => $request->request->get('email'),
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
        );

        $user = User::addUser($em, $this->get('security.encoder_factory'), $parameters);
        User::GenerateDefaultAvatar($em, $user);

        return $this->redirectToRoute('zectranet_login');
    }
}
