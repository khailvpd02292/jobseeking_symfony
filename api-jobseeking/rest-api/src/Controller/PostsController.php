<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends ApiController
{

    public function getAllPost()
    {
        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $sql = 'SELECT * FROM posts';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->response($data);
    }

    public function getPostByUser(){
        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $sql = ' SELECT p.title,p.id,p.datepost ,u.address,u.company_name,l.url FROM users u INNER JOIN posts p ON p.id_user = u.id INNER JOIN logo l ON l.id_user = u.id WHERE u.role = "ROLE_ADMIN" LIMIT 10';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->response($data);
    }

    //Retrieve posts from user list
    public function getPosts(Request $request)
    {   
        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $sql = ' SELECT p.title,p.id,p.datepost ,u.address,u.company_name,l.url FROM users u INNER JOIN posts p ON p.id_user = u.id INNER JOIN logo l ON l.id_user = u.id WHERE u.role = "ROLE_ADMIN" AND p.deadline_submission <= :dates LIMIT 10';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['dates' =>date("Y-m-d")]);
        $data = $stmt->fetchAll();
        return $this->response($data);
    }

    public function searchJob(Request $request, PostsRepository $postsRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $title = $request->get('title');
        $address = $request->get('address');
        if (empty($title) && empty($address)) {
            $this->getPosts();
        } else if (empty($title)) {
            $conn = $em->getConnection();
            if ($address == "Tất cả địa điểm") {
                $sql = ' SELECT p.title,p.id,p.datepost ,u.address,u.company_name,l.url FROM users u INNER JOIN posts p ON p.id_user = u.id INNER JOIN logo l ON l.id_user = u.id WHERE u.role = "ROLE_ADMIN" AND p.deadline_submission <= :dates LIMIT 10';
                $stmt = $conn->prepare($sql);
                $stmt->execute(['dates' =>date("Y-m-d")]);
            } else if ($address == "Đà Nẵng" || $address == "Hà Nội" || $address == "Hồ Chí Minh") {
                $sql = ' SELECT p.title,p.id,p.datepost ,u.address,u.company_name,l.url FROM users u INNER JOIN posts p ON p.id_user = u.id INNER JOIN logo l ON l.id_user = u.id WHERE u.role = "ROLE_ADMIN" AND u.address LIKE :addresss AND p.deadline_submission <= :dates';
                $stmt = $conn->prepare($sql);
                $stmt->execute(['addresss' => '%' . $address . '%',
                                'dates' =>date("Y-m-d")]);
            } else if ($address == "Địa điểm khác") {
                $sql = ' SELECT p.title,p.id,p.datepost ,u.address,u.company_name,l.url FROM users u INNER JOIN posts p ON p.id_user = u.id INNER JOIN logo l ON l.id_user = u.id WHERE u.role = "ROLE_ADMIN" AND u.address NOT IN ("Đà Nẵng","Hồ Chí Minh","Hà Nội") AND p.deadline_submission <= :dates';
                $stmt = $conn->prepare($sql);
                $stmt->execute(['dates' =>date("Y-m-d")]);
            }
        } else {
            $conn = $em->getConnection();
            if ($address == "Tất cả địa điểm") {
                $sql = ' SELECT p.title,p.id,p.datepost ,u.address,u.company_name,l.url FROM users u INNER JOIN posts p ON p.id_user = u.id INNER JOIN logo l ON l.id_user = u.id WHERE u.role = "ROLE_ADMIN" AND p.title LIKE :title AND p.deadline_submission <= :dates';
                $stmt = $conn->prepare($sql);
                $stmt->execute(['title' => '%' . $title . '%',
                                 'dates' =>date("Y-m-d")]);
            } else if ($address == "Đà Nẵng" || $address == "Hà Nội" || $address == "Hồ Chí Minh") {
                $sql = ' SELECT p.title,p.id,p.datepost ,u.address,u.company_name,l.url FROM users u INNER JOIN posts p ON p.id_user = u.id INNER JOIN logo l ON l.id_user = u.id WHERE u.role = "ROLE_ADMIN" AND p.title LIKE :title AND u.address LIKE :addresss AND p.deadline_submission <= :dates';
                $stmt = $conn->prepare($sql);
                $stmt->execute(['title' => '%' . $title . '%', 'addresss' => '%' . $address . '%','dates' =>date("Y-m-d")]);
            } else if ($address == "Địa điểm khác") {
                $sql = ' SELECT p.title,p.id,p.datepost ,u.address,u.company_name,l.url FROM users u INNER JOIN posts p ON p.id_user = u.id INNER JOIN logo l ON l.id_user = u.id WHERE u.role = "ROLE_ADMIN" AND u.address NOT IN ("Đà Nẵng","Hồ Chí Minh","Hà Nội") AND p.deadline_submission <= :dates';
                $stmt = $conn->prepare($sql);
                $stmt->execute(['dates' =>date("Y-m-d")]);
            }
        }
        $data = $stmt->fetchAll();
        return $this->response($data);

    }

    public function addPost(Request $request, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $id_user = $request->get('id_user');
        $title = $request->get('title');
        // $deadline_submission = $request->get('deadline_submission');
        $description = $request->get('description');
        $entitlements = $request->get('entitlements');
        $skill_requirements = $request->get('skill_requirements');
        if (empty($id_user)) {
            return $this->respondError('Invalid');
        }
        $user = $userRepository->findBy(
            array('id' => $id_user)
        );
        $em->getConnection()->beginTransaction();
        try {
            $datepost = new \DateTime();
            $post = new Posts();
            $post->setUser($user[0]);
            $post->setTitle($title);
            $post->setDeadline_submission($datepost);
            $post->setdescription($description);
            $post->setEntitlements($entitlements);
            $post->setSkill_requirements($skill_requirements);
            $post->setDatepost($datepost);
            $em->getConnection()->commit();
            $em->persist($post);
            $em->flush();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->close();
            return $this->respondError('Lỗi quá trình đăng bài');
        }
        return $this->respondWithSuccess(sprintf('Đăng bài thành công'));
    }

    /**
     * @param PostRepository $postRepository
     * @param $id
     * @return JsonResponse
     * @Route("find_post/{id}", name="find_post", methods={"GET"})
     */
    public function findPost(PostsRepository $postsRepository, $id)
    {
        // $em = $this->getDoctrine()->getManager();
        // $conn = $em->getConnection();
        // try {
        //     $sql = 'SELECT p.title,p.deadline_submission,p.description,p.entitlements,p.skill_requirements,u.company_name FROM posts p INNER JOIN users u ON p.id_user = u.id WHERE p.id = :idpost';
        //     $stmt = $conn->prepare($sql);
        //     $stmt->execute(['idpost' => '%' . $id . '%']);
        //     $data = $stmt->fetchAll();
        //     var_dump($data);
        //     exit;
        // } catch (\Throwable $th) {
        //     $em->getConnection()->rollback();
        //     $em->close();
        //     return $this->respondError('Lỗi');
        // }
        // if (!$data) {
        //     return $this->respondNotFound("Trang không tồn tại");
        // }
        // return $this->respondWithSuccess($data);

        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $sql = 'SELECT p.title,p.deadline_submission,p.description,p.entitlements,p.skill_requirements,u.company_name FROM posts p INNER JOIN users u ON p.id_user = u.id WHERE p.id = :id AND p.deadline_submission <= :dates';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id,'dates' =>date("Y-m-d") ]);
        $data = $stmt->fetchAll();
        return $this->response($data);
    }

    /**
     * @param PostRepository $postRepository
     * @param $id
     * @return JsonResponse
     * @Route("api/post/{id}", name="posts_delete", methods={"DELETE"})
     */
    public function deletePost(PostsRepository $postsRepository, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $postsRepository->find($id);

        if (!$post) {
            $data = [
                'errors' => "Post not found",
            ];
            return $this->response($data);
        }

        $em->remove($post);
        $em->flush();
        $data = [
            'errors' => "Post deleted successfully",
        ];
        return $this->response($data);
    }

    public function getAllCompany()
    {
        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $sql = 'SELECT u.website_company, lg.url FROM users u INNER JOIN logo lg ON u.id = lg.id_user WHERE u.role="ROLE_ADMIN" AND u.status = 1 ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->response($data);
    }

    public function searchCompany(Request $request, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $company_name = $request->get('company_name');
        if (empty($company_name)) {
            $em = $this->getDoctrine()->getManager();
            $conn = $em->getConnection();
            $sql = 'SELECT u.website_company, lg.url FROM users u INNER JOIN logo lg ON u.id = lg.id_user WHERE u.role="ROLE_ADMIN" AND u.status = 1 ';
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $this->response($data);
        } else {
            $conn = $em->getConnection();
            $sql = 'SELECT u.website_company, lg.url FROM users u INNER JOIN logo lg ON u.id = lg.id_user WHERE u.role="ROLE_ADMIN" AND u.company_name LIKE :company_name AND u.status = 1 ';
            $stmt = $conn->prepare($sql);
            $stmt->execute(['company_name' => '%' . $company_name . '%']);
            $data = $stmt->fetchAll();
            return $this->response($data);
        }
    }
}
