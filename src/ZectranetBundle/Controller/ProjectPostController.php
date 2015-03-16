<?php
namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ZectranetBundle\Entity\ProjectPost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ZectranetBundle\Entity\User;

class ProjectPostController extends Controller
{
    /**
     * @param Request $request
     * @param $project_id
     * @return Response
     */
    public function addPostAction(Request $request,$project_id)
    {
        $post = json_decode($request->getContent(), true);
        $post = (object)$post;
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $project = $this->getDoctrine()->getRepository('ZectranetBundle:Project')->find($project_id);
        $nameEpicStory = null;
        if ($project->getParent())
        {
            $nameEpicStory = $project->getName();
            $project = $project->getParent();
        }

        $new_post = ProjectPost::addNewPost($em, $user->getId(), $project_id, $post->message);

        $usersName = array();
        $privateForAll = false;
        if ($post->usersForPrivateMessage == 'all')
        {
            $this->get('zectranet.notifier')->createNotification("private_message_project", $user, $user, $project, $nameEpicStory, $post);
            $privateForAll = true;
        }

        if (($post->usersForPrivateMessage != null) and ($privateForAll == false))
        {
            $usersProjectNames = array();
            $usersProjectNames[] = $project->getOwner()->getUsername();
            foreach ($project->getUsers() as $user)
                $usersProjectNames[] = $user->getUsername();

            foreach ($project->getOffices() as $office)
            {
                $usersProjectNames[] = $office->getOwner()->getUsername();
                foreach ($office->getUsers() as $user)
                    $usersProjectNames[] = $user->getUsername();
            }

            foreach ($post->usersForPrivateMessage as $userName)
                if (in_array($userName, $usersProjectNames))
                    $usersName[] = $userName;

            if (count($usersName) > 0)
            {
                $usersEmail = $this->getDoctrine()->getRepository('ZectranetBundle:User')->findBy(array('username' => $usersName));
                $user = $this->getUser();
                $this->get('zectranet.notifier')->createNotification("private_message_project", $user, $user, $project, $nameEpicStory, $post, $usersEmail);
            }
        }

        $user = $this->getUser();
        if ($privateForAll == false)
            $this->get('zectranet.notifier')->createNotification("message_project", $user, $user, $project, $nameEpicStory, $post, $usersName);

        $response = new Response(json_encode(array('newPost' => $new_post->getInArray())));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function getPostsAction(Request $request, $project_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);
        $data = (object) $data;
        $phpPosts = ProjectPost::getPostsOffset($em,$project_id, $data->offset, $data->count);
        $jsonPosts = array();
        /** @var ProjectPost $post */
        foreach ($phpPosts as $post) {
            $jsonPosts[] = $post->getInArray();
        }
        $response = new Response(json_encode(array('Posts' => $jsonPosts)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }
}