<?php
namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
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
        $new_post = OfficePost::addNewPost($em, $user->getId(), $office_id, $post->message);

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