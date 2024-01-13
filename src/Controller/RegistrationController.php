<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_registration', methods: ['POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): JsonResponse
    {
        
        $email = $request->request->get('email');
        $name = $request->request->get('name');
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            return $this->json([
                'message' => 'User with the provided email already exists. Please use a different email address.',
            ], Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setEmail($email);
        
        $plaintextPassword = $request->request->get('password');
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setName($name);
        
        $entityManager->persist($user);
        $entityManager->flush();


        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'hashedPassword' => $hashedPassword,
        ]);
    }
}
