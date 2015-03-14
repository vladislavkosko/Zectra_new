<?php
namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ZectranetBundle\Entity\OfficePost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ZectranetBundle\Entity\User;

class OfficePostController extends Controller
{
    /**
     * @param Request $request
     * @param int $office_id
     * @return Response
     */
    public function addPostAction(Request $request, $office_id)
    {
        $post = json_decode($request->getContent(), true);
        $post = (object)$post;

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $office = $this->getDoctrine()->getRepository('ZectranetBundle:Office')->find($office_id);

        $new_post = OfficePost::addNewPost($em, $user->getId(), $office_id, $post->message);

        $usersName = array();
        $privateForAll = false;
        if ($post->usersForPrivateMessage == 'all')
        {
            $this->get('zectranet.notifier')->createNotification("private_message_office", $user, $user, $office, null, $post, null, 'office');
            $privateForAll = true;
        }

        if (($post->usersForPrivateMessage != null) and ($privateForAll == false))
        {
            $usersOffice = $office->getUsers();
            $usersOfficeNames = array();
            foreach ($usersOffice as $user)
                $usersOfficeNames[] = $user->getUsername();
            foreach ($post->usersForPrivateMessage as $userName)
                if (in_array($userName, $usersOfficeNames))
                    $usersName[] = $userName;

            if (count($usersName) > 0)
            {
                $usersEmail = $this->getDoctrine()->getRepository('ZectranetBundle:User')->findBy(array('username' => $usersName));
                $this->get('zectranet.notifier')->createNotification("private_message_office", $user, $user, $office, null, $post, $usersEmail, 'office');
            }

        }

        if ($privateForAll == false)
            $this->get('zectranet.notifier')->createNotification("message_office", $user, $user, $office, null, $post, $usersName, 'office');

        $response = new Response(json_encode(array('newPost' => $new_post->getInArray())));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    public function getPostsAction(Request $request, $office_id) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);
        $data = (object) $data;
        $phpPosts = OfficePost::getPostsOffset($em,$office_id, $data->offset, $data->count);
        $jsonPosts = array();
        /** @var OfficePost $post */
        foreach ($phpPosts as $post) {
            $jsonPosts[] = $post->getInArray();
        }

        $response = new Response(json_encode(array('Posts' => $jsonPosts)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }
}