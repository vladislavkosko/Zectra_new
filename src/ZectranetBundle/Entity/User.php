<?php

namespace ZectranetBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Filesystem\Filesystem;
use ZectranetBundle\Entity\Request;
use ZectranetBundle\Entity\Project;
use ZectranetBundle\Entity\Office;
use ZectranetBundle\Entity\Notification;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="ZectranetBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered", type="datetime")
     */
    private $registered;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastactive", type="datetime")
     */
    private $lastactive;

    /**
     * @var boolean
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="user_role",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     * @var ArrayCollection $roles
     */
    protected $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true, options={"default" = null})
     */
    private $avatar;

    /**
     * @var int
     * @ORM\Column(name="home_office_id", type="integer", nullable=true)
     */
    private $homeOfficeID;

    /**
     * @var Office
     * @ORM\OneToOne(targetEntity="Office", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="home_office_id", referencedColumnName="id")
     */
    private $homeOffice;

    /**
     * @ORM\OneToMany(targetEntity="Office", mappedBy="owner", cascade={"remove"})
     * @var ArrayCollection
     */
    private $ownedOffices;

    /**
     * @ORM\ManyToMany(targetEntity="Office", mappedBy="users")
     * @var ArrayCollection
     */
    private $assignedOffices;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="HFForum", mappedBy="users")
     */
    private $connectedHFForums;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="QnAForum", mappedBy="users")
     */
    private $connectedQnAForums;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="owner", cascade={"remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     * @var ArrayCollection
     */
    private $ownedTasks;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="assigned")
     * @ORM\OrderBy({"id" = "DESC"})
     * @var ArrayCollection
     */
    private $assignedTasks;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="owner", cascade={"remove"})
     * @ORM\OrderBy({"parentid" = "DESC"})
     * @var ArrayCollection
     */
    private $ownedProjects;

    /**
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="users")
     * @ORM\OrderBy({"parentid" = "DESC"})
     * @var ArrayCollection
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="OfficePost", mappedBy="user", cascade={"remove"})
     * @var ArrayCollection
     */
    private $postsOffice;

    /**
     * @ORM\OneToMany(targetEntity="ProjectPost", mappedBy="user", cascade={"remove"})
     * @var ArrayCollection
     */
    private $postsProject;

    /**
     * @ORM\OneToMany(targetEntity="TaskPost", mappedBy="user", cascade={"remove"})
     * @var ArrayCollection
     */
    private $postsTask;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="user", cascade={"remove"})
     * @ORM\OrderBy({"uploaded" = "DESC"})
     * @var ArrayCollection
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity="Request", mappedBy="user", cascade={"remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     * @var ArrayCollection
     */
    private $requests;

    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="user", cascade={"remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     * @var ArrayCollection
     */
    private $notifications;

    /**
     * @ORM\OneToOne(targetEntity="UserSettings", mappedBy="user", cascade={"remove"})
     * @var UserSettings
     */
    private $userSettings;

    /**
     * @ORM\OneToMany(targetEntity="SprintPermissions", mappedBy="user", cascade={"remove"})
     * @var SprintPermissions
     */
    private $userPermissions;

    /**
     * @ORM\OneToMany(targetEntity="ProjectPermissions", mappedBy="user", cascade={"remove"})
     * @var ProjectPermissions
     */
    private $userProjectPermissions;

    /**
     * @ORM\OneToMany(targetEntity="DailyTimeSheet", mappedBy="user", cascade={"remove"})
     * @ORM\OrderBy({"date" = "DESC"})
     * @var ArrayCollection
     */
    private $wde;

    /**
     * @var UserInfo
     * @ORM\OneToOne(targetEntity="UserInfo", mappedBy="user")
     */
    private $userInfo;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="user_contacts",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id")}
     * )
     * @var ArrayCollection $contacts
     */
    protected $contacts;

    /**
     * Constructor
     */
    public function __construct() {
        $this->active = true;
        $this->salt = md5(uniqid(null, true));
        $this->roles = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->connectedHFForums = new ArrayCollection();
        $this->connectedQnAForums = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    /**
     * @param EntityManager $em
     * @param int $user_id
     * @param int $contact_id
     * @return Conversation $conversation
     */
    public static function addToContactList(EntityManager $em, $user_id, $contact_id) {
        $user = $em->find('ZectranetBundle:User', $user_id);
        $contact = $em->find('ZectranetBundle:User', $contact_id);
        if (!$user->getContacts()->contains($contact)) {
            $user->addContact($contact);
            $em->persist($user);
        }
        if (!$contact->getContacts()->contains($user)) {
            $contact->addContact($user);
            $em->persist($contact);
        }
        $conversation = Conversation::getConversation($em, $user->getId(), $contact->getId());
        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setUser1($user);
            $conversation->setUser2($contact);
            $em->persist($conversation);
        }

        $em->flush();
        return $conversation;
    }

    /**
     * @param EntityManager $em
     * @param int $user_id
     * @param int $contact_id
     * @param string $message
     */
    public static function sendContactMembershipRequest(EntityManager $em, $user_id, $contact_id, $message) {
        $type = RequestType::getContactMembershipRequest($em);
        Request::addNewRequest($em, $contact_id, $type->getId(), $message, $user_id);
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return '7v8b6ghjb6834bdkjndsjb233409fjvsiu8892d';
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->name,
            $this->surname,
            $this->registered,
            $this->lastactive,
            $this->active
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->name,
            $this->surname,
            $this->registered,
            $this->lastactive,
            $this->active) = unserialize($serialized);
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() { }

    /**
     * Get inArray
     *
     * @return array
     */
    public function getInArray()
    {
        return array(
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'name' => $this->getName(),
            'surname' => $this->getSurname(),
            'registered' => $this->getRegistered(),
            'lastactive' => $this->getLastactive(),
            'active' => $this->getActive(),
            'avatar' => $this->getAvatar(),
        );
    }

    /**
     * @param EntityManager $em
     * @param int $user_id
     * @param array $params
     * @param string $email
     * @return array
     */
    public static function editProfileInfo(EntityManager $em, $user_id, $params, $email) {
        $response = array(
            'email' => false,
        );
        /** @var User $user */
        $user = $em->find('ZectranetBundle:User', $user_id);
        $user->setName($params['name']);
        $user->setSurname($params['surname']);
        $findEmail = $em->getRepository('ZectranetBundle:User')
            ->findOneBy(array('email' => $params['email']));

        if (!$findEmail) {
            $user->setEmail($params['email']);
        } else if ($email != $params['email']) {
            $response['email'] = true;
        }
        $em->persist($user);
        $em->flush();
        return $response;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set registered
     *
     * @param \DateTime $registered
     * @return User
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;

        return $this;
    }

    /**
     * Get registered
     *
     * @return \DateTime 
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Set lastactive
     *
     * @param \DateTime $lastactive
     * @return User
     */
    public function setLastactive($lastactive)
    {
        $this->lastactive = $lastactive;

        return $this;
    }

    /**
     * Get lastactive
     *
     * @return \DateTime 
     */
    public function getLastactive()
    {
        return $this->lastactive;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set roles
     *
     * @param \ZectranetBundle\Entity\Role $roles
     * @return User
     */
    public function setRoles(\ZectranetBundle\Entity\Role $roles = null)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Add roles
     *
     * @param \ZectranetBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(\ZectranetBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \ZectranetBundle\Entity\Role $roles
     */
    public function removeRole(\ZectranetBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Add ownedOffices
     *
     * @param \ZectranetBundle\Entity\Office $ownedOffices
     * @return User
     */
    public function addOwnedOffice(\ZectranetBundle\Entity\Office $ownedOffices)
    {
        $this->ownedOffices[] = $ownedOffices;

        return $this;
    }

    /**
     * Remove ownedOffices
     *
     * @param \ZectranetBundle\Entity\Office $ownedOffices
     */
    public function removeOwnedOffice(\ZectranetBundle\Entity\Office $ownedOffices)
    {
        $this->ownedOffices->removeElement($ownedOffices);
    }

    /**
     * Get ownedOffices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOwnedOffices()
    {
        return $this->ownedOffices;
    }

    /**
     * Add assignedOffices
     *
     * @param \ZectranetBundle\Entity\Office $assignedOffices
     * @return User
     */
    public function addAssignedOffice(\ZectranetBundle\Entity\Office $assignedOffices)
    {
        $this->assignedOffices[] = $assignedOffices;

        return $this;
    }

    /**
     * Remove assignedOffices
     *
     * @param \ZectranetBundle\Entity\Office $assignedOffices
     */
    public function removeAssignedOffice(\ZectranetBundle\Entity\Office $assignedOffices)
    {
        $this->assignedOffices->removeElement($assignedOffices);
    }

    /**
     * Get assignedOffices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssignedOffices()
    {
        return $this->assignedOffices;
    }

    /**
     * Add ownedTasks
     *
     * @param \ZectranetBundle\Entity\Task $ownedTasks
     * @return User
     */
    public function addOwnedTask(\ZectranetBundle\Entity\Task $ownedTasks)
    {
        $this->ownedTasks[] = $ownedTasks;

        return $this;
    }

    /**
     * Remove ownedTasks
     *
     * @param \ZectranetBundle\Entity\Task $ownedTasks
     */
    public function removeOwnedTask(\ZectranetBundle\Entity\Task $ownedTasks)
    {
        $this->ownedTasks->removeElement($ownedTasks);
    }

    /**
     * Get ownedTasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOwnedTasks()
    {
        return $this->ownedTasks;
    }

    /**
     * Add assignedTasks
     *
     * @param \ZectranetBundle\Entity\Task $assignedTasks
     * @return User
     */
    public function addAssignedTask(\ZectranetBundle\Entity\Task $assignedTasks)
    {
        $this->assignedTasks[] = $assignedTasks;

        return $this;
    }

    /**
     * Remove assignedTasks
     *
     * @param \ZectranetBundle\Entity\Task $assignedTasks
     */
    public function removeAssignedTask(\ZectranetBundle\Entity\Task $assignedTasks)
    {
        $this->assignedTasks->removeElement($assignedTasks);
    }

    /**
     * Get assignedTasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssignedTasks()
    {
        return $this->assignedTasks;
    }

    /**
     * Add ownedProjects
     *
     * @param \ZectranetBundle\Entity\Project $ownedProjects
     * @return User
     */
    public function addOwnedProject(\ZectranetBundle\Entity\Project $ownedProjects)
    {
        $this->ownedProjects[] = $ownedProjects;

        return $this;
    }

    /**
     * Remove ownedProjects
     *
     * @param \ZectranetBundle\Entity\Project $ownedProjects
     */
    public function removeOwnedProject(\ZectranetBundle\Entity\Project $ownedProjects)
    {
        $this->ownedProjects->removeElement($ownedProjects);
    }

    /**
     * Get ownedProjects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOwnedProjects()
    {
        return $this->ownedProjects;
    }

    /**
     * Add postsOffice
     *
     * @param \ZectranetBundle\Entity\OfficePost $postsOffice
     * @return User
     */
    public function addPostsOffice(\ZectranetBundle\Entity\OfficePost $postsOffice)
    {
        $this->postsOffice[] = $postsOffice;

        return $this;
    }

    /**
     * Remove postsOffice
     *
     * @param \ZectranetBundle\Entity\OfficePost $postsOffice
     */
    public function removePostsOffice(\ZectranetBundle\Entity\OfficePost $postsOffice)
    {
        $this->postsOffice->removeElement($postsOffice);
    }

    /**
     * Get postsOffice
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPostsOffice()
    {
        return $this->postsOffice;
    }

    /**
     * Add postsProject
     *
     * @param \ZectranetBundle\Entity\ProjectPost $postsProject
     * @return User
     */
    public function addPostsProject(\ZectranetBundle\Entity\ProjectPost $postsProject)
    {
        $this->postsProject[] = $postsProject;

        return $this;
    }

    /**
     * Remove postsProject
     *
     * @param \ZectranetBundle\Entity\ProjectPost $postsProject
     */
    public function removePostsProject(\ZectranetBundle\Entity\ProjectPost $postsProject)
    {
        $this->postsProject->removeElement($postsProject);
    }

    /**
     * Get postsProject
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPostsProject()
    {
        return $this->postsProject;
    }

    /**
     * Add documents
     *
     * @param \ZectranetBundle\Entity\Document $documents
     * @return User
     */
    public function addDocument(\ZectranetBundle\Entity\Document $documents)
    {
        $this->documents[] = $documents;

        return $this;
    }

    /**
     * Remove documents
     *
     * @param \ZectranetBundle\Entity\Document $documents
     */
    public function removeDocument(\ZectranetBundle\Entity\Document $documents)
    {
        $this->documents->removeElement($documents);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add notifications
     *
     * @param \ZectranetBundle\Entity\Notification $notifications
     * @return User
     */
    public function addNotification(\ZectranetBundle\Entity\Notification $notifications)
    {
        $this->notifications[] = $notifications;

        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \ZectranetBundle\Entity\Notification $notifications
     */
    public function removeNotification(\ZectranetBundle\Entity\Notification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Set userSettings
     *
     * @param \ZectranetBundle\Entity\UserSettings $userSettings
     * @return User
     */
    public function setUserSettings(\ZectranetBundle\Entity\UserSettings $userSettings = null)
    {
        $this->userSettings = $userSettings;

        return $this;
    }

    /**
     * Get userSettings
     *
     * @return \ZectranetBundle\Entity\UserSettings 
     */
    public function getUserSettings()
    {
        return $this->userSettings;
    }

    /**
     * Add projects
     *
     * @param \ZectranetBundle\Entity\Project $projects
     * @return User
     */
    public function addProject(\ZectranetBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \ZectranetBundle\Entity\Project $projects
     */
    public function removeProject(\ZectranetBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Add wde
     *
     * @param \ZectranetBundle\Entity\DailyTimeSheet $wde
     * @return User
     */
    public function addWde(\ZectranetBundle\Entity\DailyTimeSheet $wde)
    {
        $this->wde[] = $wde;

        return $this;
    }

    /**
     * Remove wde
     *
     * @param \ZectranetBundle\Entity\DailyTimeSheet $wde
     */
    public function removeWde(\ZectranetBundle\Entity\DailyTimeSheet $wde)
    {
        $this->wde->removeElement($wde);
    }

    /**
     * Get wde
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWde()
    {
        return $this->wde;
    }

    /**
     * Add postsTask
     *
     * @param \ZectranetBundle\Entity\TaskPost $postsTask
     * @return User
     */
    public function addPostsTask(\ZectranetBundle\Entity\TaskPost $postsTask)
    {
        $this->postsTask[] = $postsTask;

        return $this;
    }

    /**
     * Remove postsTask
     *
     * @param \ZectranetBundle\Entity\TaskPost $postsTask
     */
    public function removePostsTask(\ZectranetBundle\Entity\TaskPost $postsTask)
    {
        $this->postsTask->removeElement($postsTask);
    }

    /**
     * Get postsTask
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPostsTask()
    {
        return $this->postsTask;
    }

    /**
     * @param EntityManager $em
     * @param string $username
     * @return User
     */
    public static function isRegisteredByUsername($em, $username)
    {
        return $em->getRepository('ZectranetBundle:User')->findOneBy(array('username' => $username));
    }

    /**
     * @param EntityManager $em
     * @param EncoderFactory $encoderFactory
     * @param array $parameters
     * @return User
     */
    public static function addUser($em, $encoderFactory, $parameters)
    {
        /** @var User $user */
        $user = new User();
        $encoder = $encoderFactory->getEncoder($user);
        $user->setName($parameters['name']);
        $user->setSurname($parameters['surname']);
        $user->setEmail($parameters['email']);
        $user->setUsername($parameters['username']);
        $user->setPassword($encoder->encodePassword($parameters['password'], $user->getSalt()));
        $user->setRegistered(new \DateTime());
        $user->setLastActive(new \DateTime());
        $user->addRole(Role::getUserRole($em));

        $office = new Office();
        $office->setOwner($user);
        $office->setDescription("This is your home office");
        $office->setName("Home Office");
        $em->persist($office);
        
        $user->setHomeOffice($office);
        $user->setHomeOfficeID($office->getId());

        $em->persist($user);

        $settings = new UserSettings();
        $settings->setUser($user);
        $em->persist($settings);

        $userInfo = new UserInfo();
        $userInfo->setUser($user);
        $em->persist($userInfo);

        $em->flush();
        return $user;
    }

    /**
     * @param EntityManager $em
     * @param EncoderFactory $encoderFactory
     * @param User $user
     * @param array $parameters
     * @return int
     */
    public static function changePassword($em, $encoderFactory, $user, $parameters)
    {
        $encoder = $encoderFactory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($parameters['currentPassword'], $user->getSalt());
        if ($encodedPassword != $user->getPassword())
            return 0;
        if ($parameters['newPassword'] != $parameters['repeatNewPassword'])
            return 1;
        $usr = $em->getRepository('ZectranetBundle:User')->find($user->getId());
        $newPassword = $encoder->encodePassword($parameters['newPassword'], $user->getSalt());
        $usr->setPassword($newPassword);
        $em->flush();
        return 2;
    }

    /**
     * @param EntityManager $em
     * @param EncoderFactory $encoderFactory
     * @param User $user
     * @param array $parameters
     * @return int
     */
    public static function resetPassword($em, $encoderFactory, $user, $parameters)
    {
        if ($parameters['newPassword'] != $parameters['repeatNewPassword'])
            return 0;
        $encoder = $encoderFactory->getEncoder($user);
        $usr = $em->getRepository('ZectranetBundle:User')->find($user->getId());
        $newPassword = $encoder->encodePassword($parameters['newPassword'], $user->getSalt());
        $usr->setPassword($newPassword);
        $em->flush();
        return 1;
    }

    /**
     * @param EntityManager $em
     * @param User $user
     */
    public static function GenerateDefaultAvatar(EntityManager $em, User $user) {
        $fs = new Filesystem();
        // Delete existing avatar
        if ($user->getAvatar() != null) {
            $oldAvatarPath = __DIR__ . '/../../../../web/documents/' . $user->getAvatar();
            if ($fs->exists($oldAvatarPath)) $fs->remove($oldAvatarPath);
        }

        $text_colors = array(
            array(1, 201, 220), array(126, 0, 151),
            array(0, 151, 71), array(236, 126, 0),
            array(210, 8, 0), array(167, 0, 219),
            array(15, 0, 226), array(244, 0, 114),
            array(91, 162, 0), array(16, 32, 53),
            array(36, 203, 222), array(36, 80, 223),
            array(150, 36, 223), array(223, 36, 199),
            array(0, 150, 203), array(151, 100, 38),
        );

        $backgroundColor = rand(0, count($text_colors) - 1);
        $firstLetter = substr(ucfirst($user->getName()), 0, 1);
        $secondLetter = substr(ucfirst($user->getSurname()), 0, 1);
        $text = $firstLetter . $secondLetter;
        $font = __DIR__.'/../../../web/bundles/zectranet/fonts/DejaVuSansMono.ttf';
        $imagePath = __DIR__.'/../../../web/documents/' . $user->getUsername() .'/avatar/';
        $imageWidth = 150; $height = 150;
        $imageCenter = round($imageWidth/2);
        $box = imagettfbbox(60, 0, $font, $text);
        $centerPosition = $imageCenter-round(($box[2]-$box[0])/2);

        // Draw image
        $im = imagecreate($imageWidth, $height);
        imagefilledrectangle($im, 0, 0, $imageWidth, $height,
            imagecolorallocate($im, $text_colors[$backgroundColor][0],
            $text_colors[$backgroundColor][1], $text_colors[$backgroundColor][2]));
        imagettftext($im, 60, 0, $centerPosition, 105, imagecolorallocate($im, 255, 255, 255),
            $font, $firstLetter . $secondLetter);
        $datetime = new \DateTime();
        srand($datetime->format('s'));
        $newImageFile = rand(1000, 100000);
        $fs->dumpFile($imagePath . $newImageFile . '.jpeg', '');
        imagejpeg($im, $imagePath . $newImageFile . '.jpeg');

        // Set new avatar as current
        $user->setAvatar($user->getUsername() . '/avatar/' . $newImageFile . '.jpeg');
        $em->persist($user);
        $em->flush();
    }

    /**
     * Add requests
     *
     * @param \ZectranetBundle\Entity\Request $requests
     * @return User
     */
    public function addRequst(\ZectranetBundle\Entity\Request $requests)
    {
        $this->requests[] = $requests;

        return $this;
    }

    /**
     * Remove requests
     *
     * @param \ZectranetBundle\Entity\Request $requests
     */
    public function removeRequst(\ZectranetBundle\Entity\Request $requests)
    {
        $this->requests->removeElement($requests);
    }

    /**
     * Add requests
     *
     * @param \ZectranetBundle\Entity\Request $requests
     * @return User
     */
    public function addRequest(\ZectranetBundle\Entity\Request $requests)
    {
        $this->requests[] = $requests;

        return $this;
    }

    /**
     * Remove requests
     *
     * @param \ZectranetBundle\Entity\Request $requests
     */
    public function removeRequest(\ZectranetBundle\Entity\Request $requests)
    {
        $this->requests->removeElement($requests);
    }

    /**
     * Get requests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * Set homeOfficeID
     *
     * @param integer $homeOfficeID
     * @return User
     */
    public function setHomeOfficeID($homeOfficeID)
    {
        $this->homeOfficeID = $homeOfficeID;

        return $this;
    }

    /**
     * Get homeOfficeID
     *
     * @return integer 
     */
    public function getHomeOfficeID()
    {
        return $this->homeOfficeID;
    }

    /**
     * Set homeOffice
     *
     * @param \ZectranetBundle\Entity\Office $homeOffice
     * @return User
     */
    public function setHomeOffice(\ZectranetBundle\Entity\Office $homeOffice = null)
    {
        $this->homeOffice = $homeOffice;

        return $this;
    }

    /**
     * Get homeOffice
     *
     * @return \ZectranetBundle\Entity\Office 
     */
    public function getHomeOffice()
    {
        return $this->homeOffice;
    }

    /**
     * Set userInfo
     *
     * @param \ZectranetBundle\Entity\UserInfo $userInfo
     * @return User
     */
    public function setUserInfo(\ZectranetBundle\Entity\UserInfo $userInfo = null)
    {
        $this->userInfo = $userInfo;

        return $this;
    }

    /**
     * Get userInfo
     *
     * @return \ZectranetBundle\Entity\UserInfo 
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Add headerForums
     *
     * @param \ZectranetBundle\Entity\HFForum $headerForums
     * @return User
     */
    public function addHeaderForum(\ZectranetBundle\Entity\HFForum $headerForums)
    {
        $this->headerForums[] = $headerForums;

        return $this;
    }

    /**
     * Remove headerForums
     *
     * @param \ZectranetBundle\Entity\HFForum $headerForums
     */
    public function removeHeaderForum(\ZectranetBundle\Entity\HFForum $headerForums)
    {
        $this->headerForums->removeElement($headerForums);
    }

    /**
     * Get headerForums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHeaderForums()
    {
        return $this->headerForums;
    }

    /**
     * Add contacts
     *
     * @param \ZectranetBundle\Entity\User $contact
     * @return User
     */
    public function addContact(User $contact)
    {
        $this->contacts[] = $contact;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \ZectranetBundle\Entity\User $contact
     */
    public function removeContact(User $contact)
    {
        $this->contacts->removeElement($contact);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Add QnAForums
     *
     * @param \ZectranetBundle\Entity\QnAForum $qnAForums
     * @return User
     */
    public function addQnAForum(\ZectranetBundle\Entity\QnAForum $qnAForums)
    {
        $this->QnAForums[] = $qnAForums;

        return $this;
    }

    /**
     * Remove QnAForums
     *
     * @param \ZectranetBundle\Entity\QnAForum $qnAForums
     */
    public function removeQnAForum(\ZectranetBundle\Entity\QnAForum $qnAForums)
    {
        $this->QnAForums->removeElement($qnAForums);
    }

    /**
     * Get QnAForums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQnAForums()
    {
        return $this->QnAForums;
    }

    /**
     * Add connectedHFForums
     *
     * @param \ZectranetBundle\Entity\HFForum $connectedHFForums
     * @return User
     */
    public function addConnectedHFForum(\ZectranetBundle\Entity\HFForum $connectedHFForums)
    {
        $this->connectedHFForums[] = $connectedHFForums;

        return $this;
    }

    /**
     * Remove connectedHFForums
     *
     * @param \ZectranetBundle\Entity\HFForum $connectedHFForums
     */
    public function removeConnectedHFForum(\ZectranetBundle\Entity\HFForum $connectedHFForums)
    {
        $this->connectedHFForums->removeElement($connectedHFForums);
    }

    /**
     * Get connectedHFForums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConnectedHFForums()
    {
        return $this->connectedHFForums;
    }

    /**
     * Add connectedQnAForums
     *
     * @param \ZectranetBundle\Entity\QnAForum $connectedQnAForums
     * @return User
     */
    public function addConnectedQnAForum(\ZectranetBundle\Entity\QnAForum $connectedQnAForums)
    {
        $this->connectedQnAForums[] = $connectedQnAForums;

        return $this;
    }

    /**
     * Remove connectedQnAForums
     *
     * @param \ZectranetBundle\Entity\QnAForum $connectedQnAForums
     */
    public function removeConnectedQnAForum(\ZectranetBundle\Entity\QnAForum $connectedQnAForums)
    {
        $this->connectedQnAForums->removeElement($connectedQnAForums);
    }

    /**
     * Get connectedQnAForums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConnectedQnAForums()
    {
        return $this->connectedQnAForums;
    }

    /**
     * Set userPermissions
     *
     * @param \ZectranetBundle\Entity\SprintPermissions $userPermissions
     * @return User
     */
    public function setUserPermissions(\ZectranetBundle\Entity\SprintPermissions $userPermissions = null)
    {
        $this->userPermissions = $userPermissions;

        return $this;
    }

    /**
     * Get userPermissions
     *
     * @return \ZectranetBundle\Entity\SprintPermissions 
     */
    public function getUserPermissions()
    {
        return $this->userPermissions;
    }

    /**
     * Add userPermissions
     *
     * @param \ZectranetBundle\Entity\SprintPermissions $userPermissions
     * @return User
     */
    public function addUserPermission(\ZectranetBundle\Entity\SprintPermissions $userPermissions)
    {
        $this->userPermissions[] = $userPermissions;

        return $this;
    }

    /**
     * Remove userPermissions
     *
     * @param \ZectranetBundle\Entity\SprintPermissions $userPermissions
     */
    public function removeUserPermission(\ZectranetBundle\Entity\SprintPermissions $userPermissions)
    {
        $this->userPermissions->removeElement($userPermissions);
    }

    /**
     * Add userProjectPermissions
     *
     * @param \ZectranetBundle\Entity\ProjectPermissions $userProjectPermissions
     * @return User
     */
    public function addUserProjectPermission(\ZectranetBundle\Entity\ProjectPermissions $userProjectPermissions)
    {
        $this->userProjectPermissions[] = $userProjectPermissions;

        return $this;
    }

    /**
     * Remove userProjectPermissions
     *
     * @param \ZectranetBundle\Entity\ProjectPermissions $userProjectPermissions
     */
    public function removeUserProjectPermission(\ZectranetBundle\Entity\ProjectPermissions $userProjectPermissions)
    {
        $this->userProjectPermissions->removeElement($userProjectPermissions);
    }

    /**
     * Get userProjectPermissions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserProjectPermissions()
    {
        return $this->userProjectPermissions;
    }
}
