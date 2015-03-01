<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZectranetBundle\Entity\User;

class UserController extends Controller
{

    public function indexAction()
    {
        return $this->render('@Zectranet/user.html.twig');
    }

    public function generateAvatarAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        User::GenerateDefaultAvatar($em, $user);
        return $this->redirectToRoute('zectranet_user_page');
    }
}