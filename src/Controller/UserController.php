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
     * @Route("/user/login", name="user-login")
     */
    public function login(Request $request)
    {
        /**
         * @var User $user
         */
        $requestJSON = json_decode($request->getContent());
        $httpCode = 200;

        if ($requestJSON && $requestJSON->phone && $requestJSON->password) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy([
                'phone' => $requestJSON->phone,
                'password' => hash('sha256', $requestJSON->password)
            ]);

            if (!$user) {
                $response = [
                    'error' => [
                        'code' => 10002,
                        'message' => 'incorrect phone or password'
                    ]
                ];

                $httpCode = 500;
            } else {
                $token = hash('sha256', time() . $user->getPhone() . $user->getPassword());
                $user->setToken($token);

                $em->persist($user);
                $em->flush();

                $response = [
                    'user' => [
                        'phone' => $user->getPhone(),
                        'token' => $user->getToken()
                    ]
                ];
            }
        } else {
            $response = [
                'error' => [
                    'code' => 40001,
                    'message' => 'incorrect request'
                ]
            ];

            $httpCode = 500;
        }

        return new JsonResponse($response, $httpCode);
    }

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
        $requestJSON = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();
        $httpCode = 200;

        if ($requestJSON && !$requestJSON->phone) {

            $httpCode = 500;
            $result = [
                'error' => [
                    'code' => 1001,
                    'message' => 'Phone number is not correct',
                    'json' => json_encode($request->getContent())
                ]
            ];

        } else {
            //TODO: release check phone number

            try {
                $user = $em->getRepository(User::class)->findOneBy([
                    'phone' => $requestJSON->phone
                ]);

                if ($user) {
                    $httpCode = 500;

                    $result = [
                        'error' => [
                            'code' => 1000,
                            'message' => 'Phone number ' . $requestJSON->phone . ' has be register early'
                        ]
                    ];
                } else {
                    throw new NoResultException();
                }

            } catch (NoResultException $e) {
                //save new user
                $verificationCode = rand(1000, 9999);

                $user = new User();
                $user->setPhone($requestJSON->phone);
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

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/user/registration-verify-code", name="user-registration-verify-code")
     */
    public function verifyPhone(Request $request)
    {
        /**
         * @var User $user
         */
        $isDebug = $this->get('kernel')->isDebug();
        $requestJSON = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();
        $httpCode = 200;

        if ($requestJSON && !$requestJSON->code) {

            $httpCode = 500;
            $result = [
                'error' => [
                    'code' => 1002,
                    'message' => 'not correct request for verify code',
                    'json' => json_encode($request->getContent())
                ]
            ];

        } else {
            //TODO: release check phone number

            $user = $em->getRepository(User::class)->findOneBy([
                'phone' => $requestJSON->phone,
                'verificationCode' => $requestJSON->code,
                'isVerify' => false
            ]);

            if (!$user) {
                $httpCode = 500;

                $result = [
                    'error' => [
                        'code' => 1003,
                        'message' => 'not correct verify code'
                    ]
                ];
            } else {
                $user->setIsVerify(true);

                $em->persist($user);
                $em->flush();

                $result = [
                    'user' => [
                        'id' => $user->getId(),
                        'phone' => $user->getPhone()
                    ]
                ];
            }

        }

        return new JsonResponse($result, $httpCode);
    }
}
