<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Common\Constant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;

class ManagementController extends ApiController
{
    
    public function getUserCompany()
    {    
        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $sql = 'SELECT * FROM users WHERE users.role = "ROLE_ADMIN" AND users.status=0';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->response($data);
    }

    public function getUser()
    {    
        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $sql = 'SELECT * FROM users WHERE users.status = 1  ORDER BY users.role';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->response($data);
    }

    public function getByCompany(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $id = $request->get('id');
        $conn = $em->getConnection();
        $sql = 'SELECT * FROM users WHERE users.role = "ROLE_ADMIN" AND users.id = :id ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetchAll();
        return $this->response($data);
    }

    public function updateCompany(Request $request, UserRepository $userRepository,MailerInterface $mailer)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $id = $request->get('id');
        $em->getConnection()->beginTransaction();

        $user = $userRepository->find($id);
        if(!empty($user)){
            try {
                $user->setStatus("1");
                $em->getConnection()->commit();
                $em->persist($user);
                $em->flush();
            } catch (\Exception $e) {
                $em->getConnection()->rollback();
                $em->close();
                return $this->respondError('Lỗi quá trình cập nhật');
            }
        }else{
            return $this->respondError('Không tìm thấy người dùng này');
        }
        try{
            // send mail
            $email = (new Email())
            ->from(Constant::EMAIL)
            ->to($user->getUsername())
            ->subject('Đăng ký tài khoản thành công')
            ->html('<p>Tài khoản của bạn đã được phê duyệt. Bắt đầu từ bây giờ bạn có thể đăng tuyển trên </p><a href="http://127.0.0.1:8001/">Tuyendungonline</a>');

            $transport = new GmailSmtpTransport(Constant::EMAIL, Constant::PASS);
            $mailer = new Mailer($transport);
            $mailer->send($email);
        }catch(\Exception $e){
            return $this->respondError('Không thể gửi mail đến '+$user->getUsername());
        }
        return $this->respondWithSuccess('Cập nhật thành công');
    }
    public function deleteCompany(Request $request, UserRepository $userRepository)
    {

        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $id = $request->get('id');
        $em->getConnection()->beginTransaction();

        $user = $userRepository->find($id);
        if (!empty($user)) {
            try {
                $em->getConnection()->commit();
                $em->remove($user);
                $em->flush();
            } catch (\Exception $e) {
                $em->getConnection()->rollback();
                $em->close();
                return $this->respondError('Không thể xóa người dùng này');
            }
        } else {
            return $this->respondError('Không tìm thấy người dùng này');
        }
        return $this->respondWithSuccess(sprintf('Tài khoản %s đã được xóa công thành công', $user->getUsername()));
    }

    public function countCompany()
    {
        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $sql = 'SELECT ( SELECT COUNT(u1.id) FROM users u1 WHERE u1.status = 1 ) as count_company , ( SELECT COUNT(u2.id) FROM users u2 WHERE u2.status = 0 ) as count_companynew';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->response($data);
    }
}
