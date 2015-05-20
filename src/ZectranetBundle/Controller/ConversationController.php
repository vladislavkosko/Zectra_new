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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ZectranetBundle\Entity\Conversation;
use ZectranetBundle\Entity\ConversationMessage;
use ZectranetBundle\Entity\User;

class ConversationController extends Controller {
    /**
     * @Security("has_role('ROLE_USER')")
     * @param $contact_id
     * @return JsonResponse
     */
    public function getConversationAction($contact_id) {
        /** @var User $user */
        $user = $this->getUser();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $conversation = Conversation::getConversation($em, $user->getId(), $contact_id);

        $this->get('zectranet.notifier')->clearNotificationsHomeOffice($contact_id);

        return new JsonResponse(($conversation) ? $conversation->getInArray() : null);
    }

    /**
     * @param Request $request
     * @param int $conversation_id
     * @return JsonResponse
     */
    public function sendMessageAction(Request $request, $conversation_id) {
        $data = json_decode($request->getContent(), true);
        $data = (object) $data;
        /** @var User $user */
        $user = $this->getUser();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $message = null;
        try {
            if ($data->message) {
                $message = ConversationMessage::addNewMessage($em, $conversation_id, $user->getId(), $data->message);
            }
        } catch (\Exception $ex) {
            $from = 'Class: ConversationMessage, function: addNewMessage';
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
        }

        /** @var Conversation $conversation */
        $conversation = $em->find('ZectranetBundle:Conversation', $conversation_id);

        if ($conversation->getUser1ID() != $user->getId())
        {
            $office = $conversation->getUser1()->getHomeOffice();
            $resource = $this->getDoctrine()->getRepository('ZectranetBundle:User')->find($conversation->getUser1ID());
        }
        else
        {
            $office = $conversation->getUser2()->getHomeOffice();
            $resource = $this->getDoctrine()->getRepository('ZectranetBundle:User')->find($conversation->getUser2ID());
        }

        $this->get('zectranet.notifier')->createNotification("message_home_office", $user, $resource, $office, null, $data, null, null, null, $conversation_id);

        return new JsonResponse(($message) ? $message->getInArray() : null);
    }

    public function editMessageAction(Request $request, $message_id)
    {
        $data = json_decode($request->getContent(), true);
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $message = ConversationMessage::EditMessage($em, $message_id, $data['message']);
        if($message != null)
        {
            return new JsonResponse('1');
        }
    }
}