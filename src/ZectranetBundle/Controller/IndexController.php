<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ZectranetBundle\Entity\ForgotPassword;
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
     * @param null $param
     * @param null $param1
     * @return Response
     */
    public function loginAction($param = null, $param1 = null)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        $parameters = array(
            'last_username' => $lastUsername,
            'error' => $error,
        );

        if ($param != null)
            $parameters = array(
                'last_username' => $lastUsername,
                'error' => $error,
                'messageError' => $param
            );

        if ($param1 != null)
            $parameters = array(
                'last_username' => $lastUsername,
                'error' => $error,
                'messageSuccess' => $param1
            );

        return $this->render('@Zectranet/login.html.twig', $parameters);
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

    /**
     * @Route("/forgot_password")
     * @param Request $request
     * @return Response
     */
    public function forgotPasswordAction(Request $request)
    {
        $enteredEmail = $request->request->get('userEmail');
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('ZectranetBundle:User')->findOneBy(array('email' =>$enteredEmail));
        if ($user == null)
            return $this->forward('ZectranetBundle:Index:login', array('param' => "This Email isn't registered"));

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $keyForAccess = ForgotPassword::addForgotRecord($em, $user);

        $link = $this->get('router')->generate('zectranet_reset_password', array('keyForAccess' => $keyForAccess) ,true);

        $message = "Dear, " . $user->getName();
        $message .= "<br><br>To reset your password and access your account, <br> go to the following link: <br>" . $link;
        $mailer = $this->get('mailer');
        try {
            $message = $mailer->createMessage()
                ->setSubject('Zectranet Reset Your Password!')
                ->setFrom('support@zectratrading.com')
                ->setTo($user->getEmail())
                ->setBody($message, 'text/html');
            $mailer->send($message);
        } catch (\Swift_RfcComplianceException $ex) { }
        return $this->forward('ZectranetBundle:Index:login', array('param1' => "Follow the instructions in the email to reset your password"));
    }

    /**
     * @Route("/reset_password/{keyForAccess}")
     * @param $keyForAccess
     * @return Response
     * @throws  NotFoundHttpException
     */
    public function resetPasswordAction($keyForAccess)
    {
        /** @var ForgotPassword $recordFromKey */
        $recordFromKey = $this->getDoctrine()->getRepository('ZectranetBundle:ForgotPassword')->findOneBy(array('keyForAccess' => $keyForAccess));
        if (!$recordFromKey) throw new NotFoundHttpException('Sorry not existing');

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $userid = $recordFromKey->getUserid();
        $allRecords = $em->getRepository('ZectranetBundle:ForgotPassword')->findBy(array('userid' => $userid));
        foreach ($allRecords as $record)
            $em->remove($record);
        $em->flush();
        return $this->render("@Zectranet/resetPassword.html.twig", array('userid' => $userid));
    }

    /**
     * @Route("/reset_password_save/{userid}")
     * @param Request $request
     * @param $userid
     * @return Response
     */
    public function resetPasswordSaveAction(Request $request, $userid)
    {
        $newPassword = $request->request->get('newPassword');
        $repeatNewPassword = $request->request->get('repeatNewPassword');

        $parameters = array(
            'newPassword' => $newPassword,
            'repeatNewPassword' => $repeatNewPassword
        );

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('ZectranetBundle:User')->find($userid);

        $status = User::resetPassword($em, $this->get('security.encoder_factory'), $user, $parameters);

        if ($status == 0)
            return $this->render("ZectranetBundle::resetPassword.html.twig", array('userid' => $userid, 'messageError' => "Please enter the same password in both the fields"));
        return $this->forward('ZectranetBundle:Index:login', array('param1' => "Reset  password was successfully"));
    }
}
