<?php

namespace App\Controller;

use App\Entity\Curriculum_Vitae;
use App\Repository\CurriculumVitaeRepository;
use App\Repository\PostsRepository;
use Symfony\Component\HttpFoundation\Request;

class CurriculumVitaeController extends ApiController
{
    public function getCV()
    {
        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $sql = '
        SELECT * FROM cv';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->response($data);
    }

    public function uploadCV(Request $request, CurriculumVitaeRepository $curriculumVitaeRepository, PostsRepository $postsRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $id_posts = $request->get('id_posts');
        $url_cv = $request->get('url_cv');

        $description = $request->get('description');
        $status = $request->get('status');
        if (empty($id_posts) || empty($url_cv)) {
            return $this->respondError('Dữ liệu không đầy đủ');
        }
        $post = $postsRepository->findBy(
            array('id' => $id_posts)
        );
       

        $posts_id = $curriculumVitaeRepository->find($id_posts);

        if(!empty($posts_id)){
            return $this->respondError('Bạn đã nộp đơn cho bài đăng này');
        }

        if (!empty($post)) {
            $em->getConnection()->beginTransaction();
            try {
                $cv = new Curriculum_Vitae();
                $cv->setPosts($post[0]);
                $cv->setUrl_cv($url_cv);
                $cv->setDescription($description);
                $cv->setStatus($status);
                $em->persist($cv);
                $em->getConnection()->commit();
                $em->flush();
            } catch (\Exception $e) {
                $em->getConnection()->rollback();
                $em->close();
                return $this->respondError('Lỗi quá trình ứng tuyển bài');
            }
            return $this->respondWithSuccess(sprintf('Ứng tuyển thành công'));
        }
        return $this->respondError('Bài viết không tồn tại');
    }

}
