<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/user/registration-phone", name="user-registration-phone")
     */
    public function registrationPhone(Request $request)
    {
        $isDebug = $this->get('kernel')->isDebug();
        $phone = $request->query->get('phone');
        $em = $this->getDoctrine()->getManager();
        $httpCode = 200;


        if (!$phone) {

            $httpCode = 500;
            $result = [
                'error' => [
                    'code' => 1001,
                    'message' => 'Phone number is not correct'
                ]
            ];

        } else {
            //TODO: release check phone number

            try {
                $user = $em->getRepository(User::class)->findOneBy([
                    'phone' => $phone
                ]);

                $httpCode = 500;

                $result = [
                    'error' => [
                        'code' => 1000,
                        'message' => 'Phone number ' . $user->getPhone() . ' has be register early'
                    ]
                ];

            } catch (NoResultException $e) {
                //save new user
                $verificationCode = rand(1000, 9999);

                $user = new User();
                $user->setPhone($phone);
                $user->setVerificationCode($verificationCode);
                $user->setIsVerify(false);

                $em->persist($user);
                $em->flush();

                //TODO: send sms
                $result = [
                    'message' => 'Registration successful wail verification number'
                ];

                if ($isDebug) {
                    $result['verificationCode'] = $verificationCode;
                }
            }
        }

        return new JsonResponse($result, $httpCode);
    }
}