<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZectranetBundle\Entity\DailyTimeSheet;
use ZectranetBundle\Entity\User;
use ZectranetBundle\Entity\UserSettings;

class UserController extends Controller
{

    /**
     * @Route("/user")
     * @Security("has_role('ROLE_USER')")
     * @param int|null $user_id
     * @return Response
     */
    public function indexAction($user_id = null)
    {
        if ($user_id != null) {
            $user = $this->getDoctrine()->getRepository('ZectranetBundle:User')->find($user_id);
        } else {
            $user = $this->getUser();
        }
        return $this->render('@Zectranet/userProfile.html.twig', array('user' => $user));
    }

    /**
     * @Route("/user/home")
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function homeAction()
    {
        return $this->render('@Zectranet/user.html.twig');
    }

    /**
     * @Route("/user/settings")
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function settingsAction()
    {
        return $this->render('@Zectranet/settings.html.twig');
    }

    /**
     * @Route("/user/generate_avatar")
     * @Security("has_role('ROLE_USER')")
     * @return RedirectResponse
     */
    public function generateAvatarAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        User::GenerateDefaultAvatar($em, $user);
        return $this->redirectToRoute('zectranet_user_page');
    }

    /**
     * @Route("/user/settings/general")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return Response
     */
    public function generalAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var UserSettings $settings */
        $settings = $this->getUser()->getUserSettings();

        $showClosedProjects = $request->request->get('showClosedProjects');

        $settings->setShowClosedProjects(($showClosedProjects == null) ? false : true);

        $em->persist($settings);
        $em->flush();
        return $this->redirectToRoute('zectranet_user_settings');
    }

    /**
     * @Route("/user/settings/email")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return Response
     */
    public function emailAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var UserSettings $settings */
        $settings = $this->getUser()->getUserSettings();

        $parameters = array(
            'disableAllOnEmail' => $request->request->get('disableAllOnEmail'),
            'msgEmailMessageOffice' => $request->request->get('msgEmailMessageOffice'),
            'msgEmailMessageProject' => $request->request->get('msgEmailMessageProject'),
            'msgEmailMessageEpicStory' => $request->request->get('msgEmailMessageEpicStory'),
            'msgEmailMessageTask' => $request->request->get('msgEmailMessageTask'),
            'msgEmailTaskAdded' => $request->request->get('msgEmailTaskAdded'),
            'msgEmailEpicStoryAdded' => $request->request->get('msgEmailEpicStoryAdded'),
            'msgEmailTaskDeleted' => $request->request->get('msgEmailTaskDeleted'),
            'msgEmailEpicStoryDeleted' => $request->request->get('msgEmailEpicStoryDeleted')
        );

        UserSettings::setEmailSettings($em, $settings, $parameters);

        return $this->redirectToRoute('zectranet_user_settings');
    }

    /**
     * @Route("/user/settings/site")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return Response
     */
    public function siteAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var UserSettings $settings */
        $settings = $this->getUser()->getUserSettings();

        $parameters = array(
            'disableAllOnSite' => $request->request->get('disableAllOnSite'),
            'msgSiteMessageOffice' => $request->request->get('msgSiteMessageOffice'),
            'msgSiteMessageProject' => $request->request->get('msgSiteMessageProject'),
            'msgSiteMessageEpicStory' => $request->request->get('msgSiteMessageEpicStory'),
            'msgSiteMessageTask' => $request->request->get('msgSiteMessageTask'),
            'msgSiteTaskAdded' => $request->request->get('msgSiteTaskAdded'),
            'msgSiteEpicStoryAdded' => $request->request->get('msgSiteEpicStoryAdded'),
            'msgSiteTaskDeleted' => $request->request->get('msgSiteTaskDeleted'),
            'msgSiteEpicStoryDeleted' => $request->request->get('msgSiteEpicStoryDeleted')
        );

        UserSettings::setSiteSettings($em, $settings, $parameters);

        return $this->redirectToRoute('zectranet_user_settings');
    }

    /**
     * @Route("/user/settings/change_password")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return Response
     */
    public function changePasswordAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();

        $parameters = array(
            'currentPassword' => $request->request->get('currentPassword'),
            'newPassword' => $request->request->get('newPassword'),
            'repeatNewPassword' => $request->request->get('repeatNewPassword')
        );

        $status = User::changePassword($em, $this->get('security.encoder_factory'), $user, $parameters);

        if ($status == 0)
            return $this->render('@Zectranet/settings.html.twig', array('mes' => 0));
        elseif ($status == 1)
            return $this->render('@Zectranet/settings.html.twig', array('mes' => 1));
        else
            return $this->render('@Zectranet/settings.html.twig', array('mes' => 2));
    }

    /**
     * @Route("/wde")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return RedirectResponse
     */
    public function wdeAction(Request $request)
    {
        $currentDate = new \DateTime();
        $referer = $request->headers->get('referer');
        $user = $this->getUser();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $parameters = array(
            'startOffice' => $request->request->get('startOffice'),
            'startLunch' => $request->request->get('startLunch'),
            'endLunch' => $request->request->get('endLunch'),
            'endOffice' => $request->request->get('endOffice'),
            'hours' => $request->request->get('hours'),
            'mainTask' => $request->request->get('mainTask')
        );

        $currentWDE = $em->getRepository('ZectranetBundle:DailyTimeSheet')->findOneBy(array('date' => $currentDate, 'userid' => $user->getId()));
        if ($currentWDE == null)
            DailyTimeSheet::createWDE($em, $user, $parameters);
        else
            DailyTimeSheet::updateWDE($em, $user, $parameters, $currentDate);

        return new RedirectResponse($referer);
    }
}