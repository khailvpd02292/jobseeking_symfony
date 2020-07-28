<?php

namespace App\Controller;

use App\Entity\Logo;
use App\Repository\LogoRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class LogoController extends ApiController
{

    public function getAllLogo()
    {
        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $sql = '
        SELECT * FROM logo';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->response($data);
    }

    public function uploadLogo(Request $request, UserRepository $userRepository, LogoRepository $logoRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $id_user = $request->get('id_user');
        $url = $request->get('url');
        if (empty($id_user) || empty($url)) {
            return $this->respondError('Invalid');
        }
        $logo = $logoRepository->findBy(
            array('User' => $id_user)
        );

        $upload_logo = $logo[0];
        if (empty($logo)) {
            $user = $userRepository->findBy(
                array('id' => $id_user)
            );
            $logo = new Logo();
            $logo->setUser($user[0]);
            $logo->setUrl($url);
            $em->persist($logo);
            $em->flush();
            return $this->respondWithSuccess(sprintf('Upload logo successfully'));
        } else {
            $upload_logo->setUrl($url);
            $em->persist($upload_logo);
            $em->flush();
            return $this->respondWithSuccess(sprintf('Update logo successfully'));
        }
    }

}
