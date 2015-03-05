<?php

namespace ZectranetBundle\Services;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use ZectranetBundle\Entity\Notification;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\User;
use Symfony\Component\Debug\Exception\FatalErrorException;


class Notifier
{
	private $user = null;
	private $em = null;
    /** @var Router  */
	private $router = null;
	private $mailer = null;

	private $types = array(
        //for admin
        //"removed_office",
        //"removed_project",
        //"project_added",

        //for user
		"message_office",                 // +
		"message_project",                // +
        "message_epic_story",             // +
        "message_task",                   // -

        "task_added",                     // +
        "epic_story_added",               // +
        "task_deleted",                   // -
        "epic_story_deleted",             // -

        "request_office",                 // -
        "request_user_project",           // -
        "request_project",                // -
        "request_assign_task",            // -

        "private_message_office",         // -
        "private_message_project",        // -
        "private_message_epic_story",     // -
        "private_message_task",           // -
    );

	public function __construct($securityContext, $em, $router, $mailer)
	{
		$this->user = $securityContext->getToken()->getUser();
		$this->em = $em;
		$this->router = $router;
		$this->mailer = $mailer;
	}

    /**
     * @param User $user
     * @param $type
     * @param $resourceid
     * @param $destinationid
     * @param $message
     * @param null $post
     */
	private function postNotification($user, $type, $resourceid, $destinationid, $message, $post = null)
	{
		$notification = new Notification();
		$notification->setUserid($user->getId());
		$notification->setUser($user);
		$notification->setResourceid($resourceid);
		$notification->setDestinationid($destinationid);
		$notification->setType($type);

		$localMessage = $message;
		if ($post != null) {
			$localMessage = $localMessage . '<br>Message: <i>' . substr($post->message, 0, 250) . '</i>';
			if (strlen($post->message) >= 250) {
				$localMessage .= '...';
			}
		}

		$notification->setMessage($localMessage);
		$notification->setActivated(new \DateTime());
		$this->em->persist($notification);
		$this->em->flush();
		$user_settings = $user->getUserSettings();
		if(!isset($user_settings))
			return;

		switch ($type){

			case "message_office":
				$method = $user_settings->getMsgEmailMessageOffice();
				break;

			case "message_project":
				$method = $user_settings->getMsgEmailMessageProject();
				break;

			case "message_epic_story":
				$method = $user_settings->getMsgEmailMessageEpicStory();
				break;

			case "message_task":
				$method = $user_settings->getMsgEmailMessageTask();
				break;
//-----------------------------------------------------------------------------------
			case "task_added":
				$method = $user_settings->getMsgEmailTaskAdded();
				break;

			case "epic_story_added":
				$method = $user_settings->getMsgEmailEpicStoryAdded();
				break;

			case "task_deleted":
				$method = $user_settings->getMsgEmailTaskDeleted();
				break;

			case "epic_story_deleted":
				$method = $user_settings->getMsgEmailEpicStoryDeleted();
				break;
		}

        if (in_array($type, array("request_office", "request_user_project", "request_project", "request_assign_task")))
            $method = true;

        if (in_array($type, array("private_message_office", "private_message_project", "private_message_epic_story", "private_message_task")))
            $method = "no_method";

        if($method == "no_method"){
			$this->sendNotificationEmail($user, $message, $type, $destinationid, $post);
		} else {
			if($user_settings->getDisableAllOnEmail() == false ){
				if($method == true) {
					$this->sendNotificationEmail($user, $message, $type, $destinationid, $post);
				}
			}
		}
	}

    /**
     * @param User $user
     * @param $message
     * @param $type
     * @param $destinationid
     * @param null $post
     */
	private function sendNotificationEmail($user, $message, $type, $destinationid, $post = null)
	{
		if (in_array($type, array("message_office", "request_office", "private_message_office")))
            $link = $this->router->generate('zectranet_show_office', array('office_id' => $destinationid), true);

        elseif (in_array($type, array("message_task", "task_added", "task_deleted", "request_assign_task", "private_message_task")))
            $link = $this->router->generate('#', array('task_id' => $destinationid), true);

        else
            $link = $this->router->generate('zectranet_show_project', array('project_id' => $destinationid), true);

		if ($post != null) {
			$message .= "<br><br>Message:<br>". $post->message;
		}
		$message .= "<br><br>Please go to the following link: <br>" . $link;

		try {
			$message = \Swift_Message::newInstance()
				->setSubject('Zectranet notification!')
				->setFrom('notifications@zectratrading.com')
				->setTo($user->getEmail())
				->setBody($message, 'text/html');
			$this->mailer->send($message);
		} catch (\Swift_RfcComplianceException $ex) {  }
	}

    /**
     * @param $office_id
     * @return mixed
     */
	public function clearNotificationsByOfficeId($office_id)
	{
		$qb = $this->em->createQueryBuilder();
		$qb->delete('ZectranetBundle:Notification', 'n')
			->where("n.userid = :userid")
			->andWhere("n.destinationid = :destinationid")
			->andWhere("n.type = 'message_office'
    	   		    OR n.type = 'request_office'
    	   		    OR n.type = 'private_message_office'")
			->setParameter("userid", $this->user->getId())
			->setParameter("destinationid", $office_id);

		return $qb->getQuery()->getResult();
	}

    /**
     * @param $topic_id
     * @return mixed
     */
	public function clearNotificationsByProjectId($topic_id)
	{
		$qb = $this->em->createQueryBuilder();
		$qb->delete('ZectranetBundle:Notification', 'n')
			->where("n.userid = :userid")
			->andWhere("n.destinationid = :destinationid")
			->andWhere("n.type = 'message_project'
    			    OR n.type = 'request_user_project'
    			    OR n.type = 'request_project'
    			    OR n.type = 'private_message_project'
    			    OR n.type = 'message_epic_story'
    			    OR n.type = 'epic_story_added'
    			    OR n.type = 'epic_story_deleted'
    	   			OR n.type = 'private_message_epic_story'")
			->setParameter("userid", $this->user->getId())
			->setParameter("destinationid", $topic_id);

		return $qb->getQuery()->getResult();
	}

    /**
     * @param $taskId
     * @return mixed
     */
	public function clearNotificationsByTaskId($taskId)
	{
		$qb = $this->em->createQueryBuilder();
		$qb->delete('IntranetMainBundle:Notification', 'n')
			->where("n.userid = :userid")
			->andWhere("n.resourceid = :resourceid")
			->andWhere("n.type = 'message_task'
                    OR n.type = 'task_added'
                    OR n.type = 'task_deleted'
                    OR n.type = 'request_assign_task'
    				OR n.type = 'private_message_task'")
			->setParameter("userid", $this->user->getId())
			->setParameter("resourceid", $taskId);

		return $qb->getQuery()->getResult();
	}

    /**
     * @param $type
     * @param $resource
     * @param User $user
     * @param $destination
     * @param null $user_to_send_name
     * @param null $post
     * @return bool
     */
	public function createNotification($type, $resource, $user, $destination, $user_to_send_name = null, $post = null)
	{
		if (!in_array($type, $this->types)) return false;

        elseif (in_array($type, array("message_office", "message_project", "message_epic_story")))
        {
            if ($user_to_send_name != null)
                $message = 'New message from '.$resource->getName().' '.$resource->getSurname().' in "'.$user_to_send_name.'"';
            else
                $message = 'New message from '.$resource->getName().' '.$resource->getSurname().' in "'.$destination->getName().'"';
            $users = $destination->getUsers();
        }

        elseif (in_array($type, array("message_task")))
        {
            $message = 'New comment around the task "'.$resource->getName().'"';
            $users = $destination->getUsers();
            $userAsigned = $resource->getUser();
            if ($userAsigned != null) $users[] = $userAsigned;
        }

        elseif (in_array($type, array("private_message_office", "private_message_project", "private_message_epic_story", "private_message_task")))
        {
            if ($resource) {
                $message = 'New private message from ' . $resource->getName() . ' ' . $resource->getSurname() . ' in "' . $destination->getName() . '"';
                $users = User::getUserByUsername($this->em, $user_to_send_name);
            }
        }

        else
        {
            switch ($type)
            {
                case "request_assign_task":
                {
                    $message = 'You assign the new task "'.$resource->getName().'"';
                    $users = array($user);
                    break;
                }

                case "request_assign_office":
                {
                    $message = 'You assign the new office "'.$resource->getName().'"';
                    $users = array($user);
                    break;
                }

                case "request_assign_project":
                {
                    $message = 'You assign the new project "'.$resource->getName().'"';
                    $users = array($user);
                    break;
                }

                case "request_assign_epic_story":
                {
                    $message = 'You assign the new epic story "'.$resource->getName().'"';
                    $users = array($user);
                    break;
                }

                case "task_added":
                {
                    $message = 'New task in "'.$destination->getName().'"';
                    $users = $destination->getUsers();
                    break;
                }

                case "epic_story_added":
                {
                    $message = 'New epic story in "'.$destination->getName().'"';
                    $users = $destination->getUsers();
                    break;
                }

                case "task_deleted":
                {
                    $message = 'Deleted task in "'.$destination->getName().'"';
                    $users = $destination->getUsers();
                    break;
                }

                case "epic_story_deleted":
                {
                    $message = 'Deleted epic story in "'.$destination->getName().'"';
                    $users = $destination->getUsers();
                    break;
                }
            }
        }

		foreach($users as $user)
		{
			if ($user->getId() != $this->user->getId())
				$this->postNotification($user, $type, $resource->getId(), $destination->getId(), $message, $post);
		}
		return true;
	}

}
