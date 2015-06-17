<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZectranetBundle\Entity\Conversation;
use ZectranetBundle\Entity\ConversationMessage;
use ZectranetBundle\Entity\DailyTimeSheet;
use ZectranetBundle\Entity\EntityOperations;
use ZectranetBundle\Entity\HFForum;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\ProjectPermissions;
use ZectranetBundle\Entity\QnAForum;
use ZectranetBundle\Entity\Sprint;
use ZectranetBundle\Entity\SprintPermissions;
use ZectranetBundle\Entity\User;
use ZectranetBundle\Entity\UserInfo;
use ZectranetBundle\Entity\UserSettings;
use ZectranetBundle\Entity\Request as Req;

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
        $user = null;
        if ($user_id != null) {
            $user = $this->getDoctrine()->getRepository('ZectranetBundle:User')->find($user_id);
        } else {
            $user = $this->getUser();
        }

        $additionalInfo = $user->getUserInfo();
        return $this->render('@Zectranet/userProfile.html.twig',
            array('user' => $user, 'userInfo' => $additionalInfo));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @return Response
     */
    public function editProfilePageAction() {
        /** @var User $user */
        $user = $this->getUser();
        /** @var UserInfo $additionalInfo */
        $additionalInfo = $this->getDoctrine()->getRepository('ZectranetBundle:UserInfo')
            ->findOneBy(array('userID' => $user->getId()));

        $emailError = $this->get('session')->get('email');
        if ($emailError) {
            $this->get('session')->remove('email');
        }
        $errors = array(
            'email' => $emailError
        );

        return $this->render('@Zectranet/userProfileEdit.html.twig',
            array('user' => $user, 'userInfo' => $additionalInfo, 'errors' => $errors));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $user_id
     * @return RedirectResponse
     */
    public function editProfileAction (Request $request, $user_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getId() == $user_id && $request->getMethod() == "POST") {
            $params = array(
                'name' => $request->request->get('name'),
                'surname' => $request->request->get('surname'),
                'email' => $request->request->get('email'),
                'residenceCountry' => $request->request->get('residenceCountry'),
                'residenceCountryVisible' => ($request->request->get('residenceCountryVisible') == 'on'),
                'workExpirience' => $request->request->get('workExpirience'),
                'workExpirienceVisible' => ($request->request->get('workExpirienceVisible') == 'on'),
                'skills' => $request->request->get('skills'),
                'skillsVisible' => ($request->request->get('skillsVisible') == 'on'),
                'interests' => $request->request->get('interests'),
                'interestsVisible' => ($request->request->get('interestsVisible') == 'on'),
                'volunteerWork' => $request->request->get('volunteerWork'),
                'volunteerWorkVisible' => ($request->request->get('volunteerWorkVisible') == 'on'),
                'facebook' => $request->request->get('facebook'),
                'facebookVisible' => ($request->request->get('facebookVisible') == 'on'),
                'twitter' => $request->request->get('twitter'),
                'twitterVisible' => ($request->request->get('twitterVisible') == 'on'),
                'linkedIn' => $request->request->get('linkedIn'),
                'linkedInVisible' => ($request->request->get('linkedInVisible') == 'on'),
                'googlePlus' => $request->request->get('googlePlus'),
                'googlePlusVisible' => ($request->request->get('googlePlusVisible') == 'on'),
            );

            $errors = null;
            try {
                $errors = User::editProfileInfo($em, $user_id, $params, $user->getEmail());
            } catch (\Exception $ex) {
                $from = "Class: User, function: editProfileInfo";
                $this->get('zectranet.errorlogger')->registerException($ex, $from);
                if ($errors['email']) {
                    $this->get('session')->set('error', 'email');
                    return $this->redirectToRoute('zectranet_edit_user_page');
                } else {
                    return $this->redirectToRoute('zectranet_user_page');
                }
            }
            UserInfo::editInfo($em, $user_id, $params);

            if ($errors['email']) {
                $this->get('session')->set('error', 'email');
                return $this->redirectToRoute('zectranet_edit_user_page');
            } else {
                return $this->redirectToRoute('zectranet_user_page');
            }
        }
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
        try {
            User::GenerateDefaultAvatar($em, $user);
        } catch (\Exception $ex) {
            $from = "Class: User, function: GenerateDefaultAvatar";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return $this->redirectToRoute('zectranet_user_page');
        }
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

        try {
            UserSettings::setEmailSettings($em, $settings, $parameters);
        } catch (\Exception $ex) {
            $from = "Class: UserSettings, function: setEmailSettings";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return $this->redirectToRoute('zectranet_user_settings');
        }

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

        try {
            UserSettings::setSiteSettings($em, $settings, $parameters);
        } catch (\Exception $ex) {
            $from = "Class: UserSettings, function: setSiteSettings";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return $this->redirectToRoute('zectranet_user_settings');
        }

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

        $status = null;
        try {
            $status = User::changePassword($em, $this->get('security.encoder_factory'), $user, $parameters);
        } catch (\Exception $ex) {
            $from = "Class: User, function: changePassword";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return $this->render('@Zectranet/settings.html.twig', array('mes' => "ErrorLogger"));
        }

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
        try {
            if ($currentWDE == null)
                DailyTimeSheet::createWDE($em, $user, $parameters);
            else
                DailyTimeSheet::updateWDE($em, $user, $parameters, $currentDate);
        } catch (\Exception $ex) {
            $from = "Class: DailyTimeSheet, function: createWDE_or_updateWDE";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new RedirectResponse($referer);
        }

        return new RedirectResponse($referer);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return RedirectResponse
     */
    public function sendContactMembershipRequestAction(Request $request) {
        $data = (object) (json_decode($request->getContent(), true));
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        try {
            User::sendContactMembershipRequest($em, $data->app_user_id, $data->user_id, $data->message);
        } catch (\Exception $ex) {
            $from = "Class: User, function: sendContactMembershipRequest";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(-1);
        }
        return new JsonResponse(1);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $request_id
     * @return JsonResponse
     */
    public function approveContactMembershipRequestAction(Request $request, $request_id) {
        $data = json_decode($request->getContent(), true);
        $data = $data['answer'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Req $userRequest */
        $userRequest = $em->find('ZectranetBundle:Request', $request_id);

        $conversation = null;
        if ($data == 'accept') {
            try {
                /** @var Conversation $conversation */
                $conversation = User::addToContactList($em, $userRequest->getContactID(), $userRequest->getUserid());
            } catch (\Exception $ex) {
                $from = 'Class: User, function: addToContactList';
                $this->get('zectranet.errorlogger')->registerException($ex, $from);
            }
            Req::changeRequestState($em, $request_id, 2);
        } elseif($data == 'decline') {
            Req::changeRequestState($em, $request_id, 3);
        }
        elseif($data == 'more_info')
        {
            /** @var Conversation $conversation */
            $conversation = User::addToContactList($em, $userRequest->getContactID(), $userRequest->getUserid());
            $message1 = new ConversationMessage();
            $message1->setMessage($userRequest->getMessage());
            $message1->setConversation($conversation);
            $message1->setUser($userRequest->getContact());
            $newMessage2 = $userRequest->getUser()->getUsername() .' want to know more info about you.';
            $message2 = new ConversationMessage();
            $message2->setMessage($newMessage2);
            $message2->setConversation($conversation);
            $message2->setUser($userRequest->getUser());
            $em->persist($message1);
            $em->persist($message2);
            $em->flush();
            Req::changeRequestState($em, $request_id, 4);
        }
        return new JsonResponse();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @return JsonResponse
     */
    public function getContactListAction() {
        /** @var User $user */
        $user = $this->getUser();
        return new JsonResponse(EntityOperations::arrayToJsonArray($user->getContacts()));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $request_id
     * @return JsonResponse
     */
    public function approveHFForumMembershipRequestAction(Request $request, $request_id) {
        $data = json_decode($request->getContent(), true);
        $data = $data['answer'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Req $userRequest */
        $userRequest = $em->find('ZectranetBundle:Request', $request_id);
        /** @var User $user */
        $user = $this->getUser();

        $conversation = null;
        if ($data == 'accept') {
            try {
                HFForum::addUserToProject($em, $userRequest->getUserid(), $userRequest->getHFForumID());
                /** @var Conversation $conversation */
               $conversation= User::addToContactList($em, $userRequest->getContactID(), $userRequest->getUserid());
            } catch (\Exception $ex) {
                $from = 'Class: HFForum, function: addUserToProject';
                $this->get('zectranet.errorlogger')->registerException($ex, $from);
            }
            Req::changeRequestState($em, $request_id, 2);
            $event = 'User "' . $user->getUsername() . '" has joined the project';
            $this->get('zectranet.projectlogger')->logEvent($event, $userRequest->getHFForumID(), 2);
        }
        elseif($data == 'decline') {
            Req::changeRequestState($em, $request_id, 3);
        }
        elseif($data == 'more_info')
        {
            /** @var Conversation $conversation */
            $conversation= User::addToContactList($em, $userRequest->getContactID(), $userRequest->getUserid());
            $message1 = new ConversationMessage();
            $message1->setMessage($userRequest->getMessage());
            $message1->setConversation($conversation);
            $message1->setUser($userRequest->getContact());
            $newMessage2 = $userRequest->getUser()->getUsername() .' want to know more info about '. $userRequest->getHFForum()->getName();
            $message2 = new ConversationMessage();
            $message2->setMessage($newMessage2);
            $message2->setConversation($conversation);
            $message2->setUser($userRequest->getUser());
            $em->persist($message1);
            $em->persist($message2);
            $em->flush();
            Req::changeRequestState($em, $request_id, 4);
        }
        return new JsonResponse();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $request_id
     * @return JsonResponse
     */
    public function approveQnAForumMembershipRequestAction(Request $request, $request_id) {
        $data = json_decode($request->getContent(), true);
        $data = $data['answer'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Req $userRequest */
        $userRequest = $em->find('ZectranetBundle:Request', $request_id);
        /** @var User $user */
        $user = $this->getUser();
        $conversation = null;
        if ($data == 'accept') {
            try {
                QnAForum::addUserToProject($em, $userRequest->getUserid(), $userRequest->getQnAForumID());
                /** @var Conversation $conversation */
                $conversation = User::addToContactList($em, $userRequest->getContactID(), $userRequest->getUserid());
            } catch (\Exception $ex) {
                $from = 'Class: QnAForum, function: addUserToProject';
                $this->get('zectranet.errorlogger')->registerException($ex, $from);
            }
            Req::changeRequestState($em, $request_id, 2);
            $event = 'User "' . $user->getUsername() . '" has joined the project';
            $this->get('zectranet.projectlogger')->logEvent($event, $userRequest->getQnAForumID(), 1);
        }
        elseif($data == 'decline') {
            Req::changeRequestState($em, $request_id, 3);
        }
        elseif($data == 'more_info')
        {
            /** @var Conversation $conversation */
            $conversation = User::addToContactList($em, $userRequest->getContactID(), $userRequest->getUserid());
            $message1 = new ConversationMessage();
            $message1->setMessage($userRequest->getMessage());
            $message1->setConversation($conversation);
            $message1->setUser($userRequest->getContact());
            $newMessage2 = $userRequest->getUser()->getUsername() .' want to know more info about '. $userRequest->getQnAForum()->getName();
            $message2 = new ConversationMessage();
            $message2->setMessage($newMessage2);
            $message2->setConversation($conversation);
            $message2->setUser($userRequest->getUser());
            $em->persist($message1);
            $em->persist($message2);
            $em->flush();
            Req::changeRequestState($em, $request_id, 4);
        }
        return new JsonResponse();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $request_id
     * @return JsonResponse
     */
    public function approveProjectMembershipRequestAction(Request $request, $request_id) {
        $data = json_decode($request->getContent(), true);
        $data = $data['answer'];
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Req $userRequest */
        $userRequest = $em->find('ZectranetBundle:Request', $request_id);
        /** @var User $user */
        $user = $this->getUser();
        $conversation = null;
        if ($data == 'accept') {
            try {
                Project::addUserToProject($em, $userRequest->getUserid(), $userRequest->getProject()->getId());
                /** @var Conversation $conversation */
                $conversation = User::addToContactList($em, $userRequest->getContactID(), $userRequest->getUserid());

                SprintPermissions::addPermission($em, $userRequest->getProject()->getSprints(), $userRequest->getUser());
                ProjectPermissions::addPermission($em, $userRequest->getProject(), $userRequest->getUser());

            } catch (\Exception $ex) {
                $from = 'Class: Project, function: addUserToProject';
                $this->get('zectranet.errorlogger')->registerException($ex, $from);
            }
            Req::changeRequestState($em, $request_id, 2);
            $event = 'User "' . $user->getUsername() . '" has joined the project';
            $this->get('zectranet.projectlogger')->logEvent($event, $userRequest->getProject()->getId(), 1);
        }
        elseif($data == 'decline') {
            Req::changeRequestState($em, $request_id, 3);
        }
        elseif($data == 'more_info')
        {
            /** @var Conversation $conversation */
            $conversation = User::addToContactList($em, $userRequest->getContactID(), $userRequest->getUserid());
            $message1 = new ConversationMessage();
            $message1->setMessage($userRequest->getMessage());
            $message1->setConversation($conversation);
            $message1->setUser($userRequest->getContact());
            $newMessage2 = $userRequest->getUser()->getUsername() .' want to know more info about '. $userRequest->getProject()->getName();
            $message2 = new ConversationMessage();
            $message2->setMessage($newMessage2);
            $message2->setConversation($conversation);
            $message2->setUser($userRequest->getUser());
            $em->persist($message1);
            $em->persist($message2);
            $em->flush();
            Req::changeRequestState($em, $request_id, 4);
        }
        return new JsonResponse();
    }
}