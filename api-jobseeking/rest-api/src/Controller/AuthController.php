<?php

namespace App\Controller;

use App\Entity\Logo;
use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
class AuthController extends ApiController
{
    public function register(Request $request, UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $username = $request->get('username');
        $password = $request->get('password');
        $role = $request->get('role');
        $fullname = $request->get('fullname');
        $phone = $request->get('phone');
        $gender = $request->get('gender') == "0" ? true : false;
        $address = $request->get('address');
        $company_name = $request->get('company_name');
        $status = $request->get('status');
        $website_company = $request->get('website_company');
        $birthday = new \DateTime($request->get('birthday'));

        if (empty($username) || empty($password) || empty($role) || empty($fullname) || empty($phone)
            || empty($gender)) {
            return $this->respondError("Thông tin đăng ký không đầy đủ");
        }
        if (strlen($password) < 6) {
            return $this->respondError("Mật khẩu phải có ít nhất 6 ký tự.");
        }
        $email = $userRepository->findBy(
            array('username' => $username)
        );
        if (!empty($email[0])) {
            return $this->respondError(sprintf('Tài khoản %s đã tồn tại', $username));
        } else {
            $em->getConnection()->beginTransaction();
            try {
                $user = new User($username);
                $user->setPassword($encoder->encodePassword($user, $password));
                $user->setUsername($username);
                $user->setRole($role);
                $user->setFullname($fullname);
                $user->setPhone($phone);
                $user->setGender($gender);
                $user->setAddress($address);
                $user->setCompany_name($company_name);
                $user->setStatus($status);
                $user->setWebsite_company($website_company);
                $user->setBirthday($birthday);
                $em->persist($user);       
                if (!empty($company_name)) {
                    $logo = new Logo();
                    $logo->setUser($user);
                    $logo->setUrl("");
                    $em->persist($logo);
                }
                $em->getConnection()->commit();
                $em->flush();
            } catch (\Throwable $th) {
                $em->getConnection()->rollback();
                $em->close();
                return $this->respondError(sprintf('Tài khoản %s đăng ký không thành công ', $username));
            }
        }
        return $this->respondWithSuccess(sprintf('Tài khoản %s đăng ký thành công', $user->getUsername()));

    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(Request $request, UserInterface $user, JWTTokenManagerInterface $JWTManager)
    { 
        //  $request = $this->transformJsonBody($request);
        // $username = $request->get('username');
        // $user = new User($username);
        // var_dump($user);
        // exit;
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }

   

    // public function login(Request $request)
    // {
    //     if ($this->authorizationChecker->isGranted('ROLE_USER')) {
    //         // return $this->redirectToRoute('admin_homepage');
    //         var_dump('a');
    //         exit;
    //     }
    //     var_dump($this->helper->getLastAuthenticationError());
    //         exit;
    //     // return [
    //     //     'error' => $this->helper->getLastAuthenticationError()
    //     // ];
    // }

    // public static function getSubscribedEvents()
    // {
    //     return [
    //         Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
    //         Events::JWT_AUTHENTICATED => 'onAuthenticatedAccess',
    //         KernelEvents::RESPONSE => 'onAuthenticatedResponse',
    //     ];
    // }
    // public function onAuthenticatedResponse(FilterResponseEvent $event,UserInterface $user)
    // {
    //     if ($this->payload && $user) {
    //         $expireTime = $this->payload['exp'] - time();
    //         if ($expireTime < static::REFRESH_TIME) {
    //             // Refresh token
    //             $jwt = $this->jwtManager->create($this->user);
    //             $response = $event->getResponse();
    //             // Set cookie
    //             $this->createCookie($response, $jwt);
    //         }
    //     }
    // }
    // public function onAuthenticatedAccess(JWTAuthenticatedEvent $event,UserInterface $user)
    // {
    //     $this->payload = $event->getPayload();
    //     $user = $event->getToken()->getUser();
    // }

    // public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    // {
    //     $eventData = $event->getData();
    //     if (isset($eventData['token'])) {
    //         $response = $event->getResponse();
    //         $jwt = $eventData['token'];
    //         // Set cookie
    //         $this->createCookie($response, $jwt);
    //     }
    //     return new JsonResponse(['token' => $jwt]);
    // }
    // protected function createCookie(Response $response, $jwt)
    // {
    //     $response->headers->setCookie(
    //         new Cookie(
    //             "BEARER",
    //             $jwt,
    //             new \DateTime("+1 day"),
    //             "/",
    //             null,
    //             false,
    //             true,
    //             false,
    //             'strict'
    //         )
    //     );

    // }
}
