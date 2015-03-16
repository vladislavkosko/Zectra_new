<?php

namespace ZectranetBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use ZectranetBundle\Entity\Notification;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\Task;
use ZectranetBundle\Entity\User;
use Symfony\Component\Debug\Exception\FatalErrorException;


class Notifier
{
	/** @var User $user */
	private $user;
	/** @var EntityManager $em */
	private $em;
    /** @var Router $router */
	private $router;
	/** @var \Swift_Mailer $mailer */
	private $mailer;

	/** @var array $types */
	private $types = array(
        //for admin
        //"removed_office",
        //"removed_project",
        //"project_added",

        //for user
		"message_office",                 // +
		"message_project",                // +
        "message_epic_story",             // +
        "message_task",                   // +

        "task_added",                     // +
        "epic_story_added",               // +
        "task_deleted",                   // +
        "epic_story_deleted",             // +

        "request_office",                 // -
        "request_user_project",           // +
        "request_project",                // -
        "request_assign_task",            // -

        "private_message_office",         // +
        "private_message_project",        // +
        "private_message_epic_story",     // +
        "private_message_task",           // +
    );

	/**
	 * @param TokenStorage $tokenStorage
	 * @param EntityManager $em
	 * @param Router $router
	 * @param \Swift_Mailer $mailer
	 */
	public function __construct(TokenStorage $tokenStorage, EntityManager $em, Router $router, \Swift_Mailer $mailer)
	{
		$this->user = $tokenStorage->getToken()->getUser();
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
     * @param object|null $post
     */
	private function postNotification($user, $type, $resourceid, $destinationid, $message, $post = null)
	{
		$notification = new Notification();
		$notification->setUserid($user->getId());
		$notification->setUser($user);
		$notification->setResourceid($resourceid);
		$notification->setDestinationid($destinationid);
		$notification->setType($type);

        $message = preg_replace("/<[\W\w]{1,255}>/", "", $message);
		$localMessage = $message;
		if ($post != null) {
			$localMessage = $localMessage . '<br>Message: <i>'
                . substr(preg_replace("/<[\W\w]{1,255}>/", "", $post->message), 0, 100)
                . '</i>';
			if (strlen($post->message) >= 100) {
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

		$method = null;

		switch ($type){

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
            $method = true;

        if (in_array($type, array("message_office", "message_project", "message_epic_story", "message_task")))
            $method = false;

        if($method == true)
			$this->sendNotificationEmail($user, $message, $type, $destinationid, $post);
		else
        {
			if(($user_settings->getDisableAllOnEmail() == false) and ($method == true))
                $this->sendNotificationEmail($user, $message, $type, $destinationid, $post);
        }
	}

    /**
     * @param User $user
     * @param $message
     * @param $type
     * @param $destinationid
     * @param object|null $post
     */
	private function sendNotificationEmail($user, $message, $type, $destinationid, $post = null)
	{
		if (in_array($type, array("message_office", "request_office", "private_message_office")))
            $link = $this->router->generate('zectranet_show_office', array('office_id' => $destinationid), true);

        elseif (in_array($type, array("message_task", "request_assign_task", "private_message_task")))
            $link = $this->router->generate('zectranet_task_show', array('task_id' => $destinationid), true);

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
			->andWhere("n.type = 'message_office' OR n.type = 'private_message_office'")
			->setParameter("userid", $this->user->getId())
			->setParameter("destinationid", $office_id);

		return $qb->getQuery()->getResult();
	}

    /**
     * @param $office_id
     * @return mixed
     */
    public function clearAllNotificationsByOfficeId($office_id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->delete('ZectranetBundle:Notification', 'n')
            ->where("n.destinationid = :destinationid")
            ->andWhere("n.type = 'message_office' OR n.type = 'private_message_office'")
            ->setParameter("destinationid", $office_id);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $project_id
     * @return mixed
     */
	public function clearNotificationsByProjectId($project_id)
	{
		$qb = $this->em->createQueryBuilder();
		$qb->delete('ZectranetBundle:Notification', 'n')
			->where("n.userid = :userid")
			->andWhere("n.destinationid = :destinationid")
			->andWhere("n.type = 'message_project'
    			    OR n.type = 'private_message_project'
    			    OR n.type = 'message_epic_story'
    			    OR n.type = 'private_message_epic_story'
    			    OR n.type = 'epic_story_added'
    			    OR n.type = 'epic_story_deleted'
    			    OR n.type = 'task_added'
                    OR n.type = 'task_deleted'")
			->setParameter("userid", $this->user->getId())
			->setParameter("destinationid", $project_id);

		return $qb->getQuery()->getResult();
	}

    /**
     * @param $project_id
     * @return mixed
     */
    public function clearAllNotificationsByProjectId($project_id)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->delete('ZectranetBundle:Notification', 'n')
            ->where("n.destinationid = :destinationid")
            ->andWhere("n.type = 'message_project'
    			    OR n.type = 'private_message_project'
    			    OR n.type = 'message_epic_story'
    			    OR n.type = 'private_message_epic_story'
    			    OR n.type = 'epic_story_added'
    			    OR n.type = 'epic_story_deleted'
    			    OR n.type = 'task_added'
                    OR n.type = 'task_deleted'")
            ->setParameter("destinationid", $project_id);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $taskId
     * @return mixed
     */
	public function clearAllNotificationsByTaskId($taskId)
	{
		$qb = $this->em->createQueryBuilder();
		$qb->delete('ZectranetBundle:Notification', 'n')
            ->where("n.destinationid = :destinationid
                AND (n.type = 'message_task' OR n.type = 'private_message_task')")
			->setParameter("destinationid", $taskId);

		return $qb->getQuery()->getResult();
	}

    /**
     * @param $taskId
     * @return mixed
     */
    public function clearNotificationsByTaskId($taskId)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->delete('ZectranetBundle:Notification', 'n')
            ->where("n.userid = :userid
                AND n.destinationid = :destinationid
                AND (n.type = 'message_task' OR n.type = 'private_message_task')")
            ->setParameter("userid", $this->user->getId())
            ->setParameter("destinationid", $taskId);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $type
     * @param User|Project|Task $resource
     * @param $usersRequest
     * @param Project|Office $destination
     * @param string|null $user_to_send_name
     * @param object|null $post
     * @param array|null $temp
     * @param null $isOffice
     * @param null $task
     * @return bool
     */
	public function createNotification($type, $resource, $usersRequest, $destination, $user_to_send_name = null, $post = null, $temp = null, $isOffice = null, $task = null)
	{
		$users = null;
		$message = null;

		if (!in_array($type, $this->types)) return false;

        elseif (in_array($type, array("message_office", "message_project", "message_epic_story")))
        {
            if ($user_to_send_name != null)
                $message = 'New message from '.$resource->getName().' '.$resource->getSurname().' in "'.$user_to_send_name.'"';
            else
                $message = 'New message from '.$resource->getName().' '.$resource->getSurname().' in "'.$destination->getName().'"';
            if (count($temp) > 0)
            {
                $users = array();
                foreach ($destination->getUsers() as $user)
                    if (!in_array($user->getUsername(), $temp)) $users[] = $user;

                if (!in_array($destination->getOwner()->getUsername(), $temp))
                    $users[] = $destination->getOwner();

                if (($isOffice == null) and (count($destination->getOffices())) > 0)
                {
                    foreach ($destination->getOffices() as $office)
                    {
                        if ($this->user->getUsername() != $office->getOwner()->getUsername())
                            $users[] = $office->getOwner();
                        foreach ($office->getUsers() as $user)
                            if (!in_array($user->getUsername(), $temp)) $users[] = $user;
                    }
                }
            }
            else
            {
                $users = array();
                foreach ($destination->getUsers() as $user)
                    $users[] = $user;
                if ($this->user->getUsername() != $destination->getOwner()->getUsername())
                    $users[] = $destination->getOwner();

                if (($isOffice == null) and (count($destination->getOffices())) > 0)
                {
                    foreach ($destination->getOffices() as $office)
                    {
                        if ($this->user->getUsername() != $office->getOwner()->getUsername())
                            $users[] = $office->getOwner();
                        foreach ($office->getUsers() as $user)
                            $users[] = $user;
                    }
                }
            }
        }

        elseif (in_array($type, array("message_task")))
        {
            if ($user_to_send_name != null)
                $message = 'New comment around the task ' . $task->getName() . ' from '.$resource->getName().' '.$resource->getSurname().' in "'.$user_to_send_name.'"';
            else
                $message = 'New comment around the task ' . $task->getName() . ' from '.$resource->getName().' '.$resource->getSurname().' in "'.$destination->getName().'"';

            if (count($temp) > 0)
            {
                $users = array();
                foreach ($destination->getUsers() as $user)
                    if (!in_array($user->getUsername(), $temp)) $users[] = $user;

                if (!in_array($destination->getOwner()->getUsername(), $temp))
                    $users[] = $destination->getOwner();

                foreach ($destination->getOffices() as $office)
                {
                    if ($this->user->getUsername() != $office->getOwner()->getUsername())
                        $users[] = $office->getOwner();
                    foreach ($office->getUsers() as $user)
                        if (!in_array($user->getUsername(), $temp)) $users[] = $user;
                }
            }
            else
            {
                $users = array();
                foreach ($destination->getUsers() as $user)
                    $users[] = $user;
                if ($this->user->getUsername() != $destination->getOwner()->getUsername())
                    $users[] = $destination->getOwner();

                foreach ($destination->getOffices() as $office)
                {
                    if ($this->user->getUsername() != $office->getOwner()->getUsername())
                        $users[] = $office->getOwner();
                    foreach ($office->getUsers() as $user)
                        $users[] = $user;
                }
            }
            $destination = $task;
        }

        elseif (in_array($type, array("private_message_office", "private_message_project", "private_message_epic_story", "private_message_task")))
        {
            if ($task != null)
            {
                if ($user_to_send_name != null)
                    $message = 'New private comment around the task ' . $task->getName() . ' from '.$resource->getName().' '.$resource->getSurname().' in "'.$user_to_send_name.'"';
                else
                    $message = 'New private comment around the task ' . $task->getName() . ' from '.$resource->getName().' '.$resource->getSurname().' in "'.$destination->getName().'"';
            }

            else
            {
                if ($user_to_send_name != null)
                    $message = 'New private message from ' . $resource->getName() . ' ' . $resource->getSurname() . ' in "' . $user_to_send_name . '"';
                else
                    $message = 'New private message from ' . $resource->getName() . ' ' . $resource->getSurname() . ' in "' . $destination->getName() . '"';
            }

            if ($temp != null) $users = $temp;
            else
            {
                $users = array();
                foreach ($destination->getUsers() as $user)
                    $users[] = $user;
                if ($this->user->getUsername() != $destination->getOwner()->getUsername())
                    $users[] = $destination->getOwner();

                if (($isOffice == null) and (count($destination->getOffices())) > 0)
                {
                    foreach ($destination->getOffices() as $office)
                    {
                        if ($this->user->getUsername() != $office->getOwner()->getUsername())
                            $users[] = $office->getOwner();
                        foreach ($office->getUsers() as $user)
                            $users[] = $user;
                    }
                }
            }
            if ($task != null) $destination = $task;
        }

        else
        {
            switch ($type)
            {
                case "request_office":
                {
                    $message = 'You assign the new task "'.$resource->getName().'"';
                    $users = $usersRequest;
                    break;
                }

                case "request_user_project":
                {
                    $message = 'You have a new assign request from "'.$resource->getName() . ' ' . $resource->getSurname() . ' in "' . $destination->getName() . '"';
                    $users = $usersRequest;
                    break;
                }

                case "request_project":
                {
                    $message = 'You assign the new project "'.$resource->getName().'"';
                    $users = $usersRequest;
                    break;
                }

                case "request_assign_task":
                {
                    $message = 'You assign the new epic story "'.$resource->getName().'"';
                    $users = $usersRequest;
                    break;
                }

                case "task_added":
                {
                    if ($user_to_send_name != null)
                        $message = 'New task "'. $temp . '"' . ' in "'.$user_to_send_name.'"';
                    else
                        $message = 'New task "'. $temp . '"' . ' in "'.$destination->getName().'"';

                    $users = array();
                    foreach ($destination->getUsers() as $user)
                        $users[] = $user;

                    if ($this->user->getUsername() != $destination->getOwner()->getUsername())
                        $users[] = $destination->getOwner();

                    foreach ($destination->getOffices() as $office)
                    {
                        if ($this->user->getUsername() != $office->getOwner()->getUsername())
                            $users[] = $office->getOwner();
                        foreach ($office->getUsers() as $user)
                            $users[] = $user;
                    }
                    break;
                }

                case "epic_story_added":
                {
                    $message = 'New epic story "'. $temp . '"' . ' in "' . $destination->getName().'"';

                    $users = array();
                    foreach ($destination->getUsers() as $user)
                        $users[] = $user;

                    if ($this->user->getUsername() != $destination->getOwner()->getUsername())
                        $users[] = $destination->getOwner();

                    foreach ($destination->getOffices() as $office)
                    {
                        if ($this->user->getUsername() != $office->getOwner()->getUsername())
                            $users[] = $office->getOwner();
                        foreach ($office->getUsers() as $user)
                            $users[] = $user;
                    }
                    break;
                }

                case "task_deleted":
                {
                    if ($user_to_send_name != null)
                        $message = 'Deleted task "'. $temp . '"' . ' in "'.$user_to_send_name.'"';
                    else
                        $message = 'Deleted task "'. $temp . '"' . ' in "'.$destination->getName().'"';

                    $users = array();
                    foreach ($destination->getUsers() as $user)
                        $users[] = $user;

                    if ($this->user->getUsername() != $destination->getOwner()->getUsername())
                        $users[] = $destination->getOwner();

                    foreach ($destination->getOffices() as $office)
                    {
                        if ($this->user->getUsername() != $office->getOwner()->getUsername())
                            $users[] = $office->getOwner();
                        foreach ($office->getUsers() as $user)
                            $users[] = $user;
                    }
                    break;
                }

                case "epic_story_deleted":
                {
                    $message = 'Deleted epic story "'. $temp . '"' . ' in "'.$destination->getName().'"';

                    $users = array();
                    foreach ($destination->getUsers() as $user)
                        $users[] = $user;

                    if ($this->user->getUsername() != $destination->getOwner()->getUsername())
                        $users[] = $destination->getOwner();

                    foreach ($destination->getOffices() as $office)
                    {
                        if ($this->user->getUsername() != $office->getOwner()->getUsername())
                            $users[] = $office->getOwner();
                        foreach ($office->getUsers() as $user)
                            $users[] = $user;
                    }
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
