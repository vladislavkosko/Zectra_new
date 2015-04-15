<?php

namespace ZectranetBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZectranetBundle\Entity\User;
use ZectranetBundle\Entity\UserSettings;
use ZectranetBundle\Entity\Document;
use Symfony\Component\Filesystem\Filesystem;

class UploadController extends Controller
{
    /**
     * @Route("/upload")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return RedirectResponse
     */
    public function indexAction(Request $request)
    {
        return $this->render('@Zectranet/upload.html.twig');
    }


    /**
     * @param Request $request
     * @return Response
     */
    public function uploadAction(Request $request)
    {
        $user = $this->getUser();

        $allowed = array('doc', 'docx', 'xls', 'xlsx',
            'jpg', 'jpeg', 'gif', 'png',
            'avi', 'pdf', 'mp3', 'zip',
            'txt', 'xml', 'xps', 'rtf',
            'odt', 'htm', 'html', 'ods');
        $file = $request->files->get('upl');

        if($file == null)
        {
            $response = new Response(json_encode(array("message" => "bad")));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $extension = $file->getClientOriginalExtension();

            $file_name = str_replace(' ', '_', $file->getClientOriginalName());

            if (in_array($extension, $allowed)) {
                $em = $this->getDoctrine();
                $document_clone = $em->getRepository('ZectranetBundle:Document')->findByName($file_name);
                if (count($document_clone) == 0) {
                    $FS = new Filesystem();
                    if (!$FS->exists(__DIR__ . '/../../../web/documents/' . $user->getUsername())) {
                        $FS->mkdir(__DIR__ . '/../../../web/documents/' . $user->getUsername());
                    }

                    $file->move(__DIR__ . '/../../../web/documents/' . $user->getUsername(), $file_name);

                    try {
                        $document = new Document();
                        $document->setName($file_name);
                        $document->setPath($user->getUsername() . '/' . $file_name);
                        $document->setUserid($user->getId());
                        $document->setUser($user);
                        $document->setUploaded(new \DateTime());
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($document);
                        $em->flush();
                    } catch (\Exception $ex) {
                        $from = "Class: Document, function: uploadAction";
                        $this->get('zectranet.errorlogger')->registerException($ex, $from);
                        return new JsonResponse(false);
                    }

                    $response = new Response(json_encode(array("message" => "success")));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                } else {
                    $FS = new Filesystem();
                    if (!$FS->exists(__DIR__ . '/../../../web/documents/' . $user->getUsername())) {
                        $FS->mkdir(__DIR__ . '/../../../web/documents/' . $user->getUsername());
                    }

                    $file_name = basename($file_name, $extension);
                    $file_name = substr($file_name, 0, strlen($file_name) - 1);
                    $file_name = $file_name . rand(0, 9999);
                    $file_name = $file_name . '.' . $extension;

                    $file->move(__DIR__ . '/../../../web/documents/' . $user->getUsername(), $file_name);

                    try {
                        $document = new Document();
                        $document->setName($file_name);
                        $document->setPath($user->getUsername() . '/' . $file_name);
                        $document->setUserid($user->getId());
                        $document->setUser($user);
                        $document->setUploaded(new \DateTime());
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($document);
                        $em->flush();
                    } catch (\Exception $ex) {
                        $from = "Class: Document, function: uploadAction";
                        $this->get('zectranet.errorlogger')->registerException($ex, $from);
                        return new JsonResponse(false);
                    }

                    $response = new Response(json_encode(array("message" => 'success')));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } else {
                $response = new Response(json_encode(array("message" => $extension)));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
    }

    public function Insert_Screenshots_InPHPAction()
    {
        $user_name = $this->getUser()->GetUserName();
        $user = $this->getUser();
        $imgs_in_base64 = json_decode(trim(file_get_contents('php://input')), true);

        $FS = new Filesystem();
        if (!$FS->exists(__DIR__ . '/../../../web/documents/' . $user_name.'/'.'attachments')) {
            $FS->mkdir(__DIR__ . '/../../../web/documents/' .$user_name.'/'.'attachments');
        }
        $documents = array();
        foreach($imgs_in_base64 as $img_in_base64 ) {
            $in_bd = 0;
            while($in_bd == 0 )
            {
                $em = $this->getDoctrine();
                $name_img = rand(0, 999999);
                $document_clone = $em->getRepository('ZectranetBundle:Document')->findByName($name_img . '.png');

                if (count($document_clone) == 0)
                {
                    try {
                        $document = new Document($user);
                        $document->setName($name_img . '.png');
                        $document->setPath($user_name . '/' . 'attachments/' . $name_img . ".png");
                        $document->setUserid($user->getId());
                        $document->setUser($user);
                        $document->setUploaded(new \DateTime());
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($document);
                        $em->flush();
                    } catch (\Exception $ex) {
                        $from = "Class: Document, function: Insert_Screenshots_InPHPAction";
                        $this->get('zectranet.errorlogger')->registerException($ex, $from);
                        return new JsonResponse(false);
                    }

                    $img_in_base64 = str_replace('data:image/png;base64,', '', $img_in_base64);
                    $img_in_base64 = str_replace(' ', '+', $img_in_base64);
                    $img_in_base64 = base64_decode($img_in_base64);

                    $fpng = fopen(__DIR__ . '/../../../web/documents/' . $user_name . '/' . 'attachments/' . $name_img . ".png", "w");
                    $path_to_img = __DIR__ . '/../../../web/documents/' . $user_name . '/' . 'attachments/' . $name_img . ".png";
                    fwrite($fpng, $img_in_base64);
                    fclose($fpng);

                    $documents[] = $document;

                    $in_bd =1;
                }
                else
                    {
                        $in_bd =0;
                    }
            }
        }
        $new_doc = null;
        foreach($documents as $document)
        {
            $new_doc = $document->getInArray();
        }
        $response = new Response(json_encode(array("result" => $new_doc)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function getDocumentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $userid = $this->getUser()->getId();

        $documents = null;
        try {
            $documents = Document::getAllDocuments($em, $userid);
        } catch (\Exception $ex) {
            $from = "Class: Document, function: getAllDocuments";
            $this->get('zectranet.errorlogger')->registerException($ex, $from);
            return new JsonResponse(false);
        }
        $response = new Response(json_encode(array("result" => $documents)));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    public function removeDocAction($fileid)
    {
        $filesystem = new Filesystem();
        $em = $this->getDoctrine()->getManager();
        $file = $em->getRepository('ZectranetBundle:Document')->find($fileid);
        $filesystem->remove($file->getAbsolutePath());
        $em->remove($file);
        $em->flush();
        $response = new Response(json_encode(array("message" => "OK")));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function renameDocAction($fileid, Request $request)
    {
        $filesystem = new Filesystem();
        $data = json_decode($request->getContent(), true);

        $em = $this->getDoctrine()->getManager();
        $file = $em->getRepository('ZectranetBundle:Document')->find($fileid);
        $exist_file = new \Symfony\Component\HttpFoundation\File\File($file->getAbsolutePath());
        $old_name = $exist_file->getBasename('.' . $exist_file->getExtension());

        $old_url = $file->getAbsolutePath();
        $new_url = str_replace($old_name, $data['NewName'], $old_url);
        try {
            $filesystem->rename($old_url, $new_url);
        }
        catch (IOException $ex) {
            $response = new Response(json_encode(array("message" => "exists")));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $file->setName($data['NewName'] . '.' . $exist_file->getExtension());
        $file->setPath(str_replace($old_name, $data['NewName'], $file->getPath()));
        $em->flush();

        $response = new Response(json_encode(array("message" => "OK")));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}

