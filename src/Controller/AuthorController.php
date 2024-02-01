<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/authors')]
class AuthorController extends AbstractController
{
    #[Route('/', name: 'author_index')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $authors = $entityManager->getRepository(Author::class)->findAll();

        return $this->json($authors);
    }

    #[Route('/new', name: 'author_new', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access this route.')]
    public function new(Request $request, #[CurrentUser] ?User $user, EntityManagerInterface $entityManager): Response
    {
        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['name'])) {
            throw new \InvalidArgumentException('"name" must be provided for create.');
        }
        
        try {
            $author = new Author();
            $author->setName($requestData['name']);
            
            $entityManager->persist($author);
            $entityManager->flush();
            
            return $this->json(['message' => 'Saved new author with id ' . $author->getId()]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An error occurred while saving the author.'], Response::HTTP_BAD_REQUEST);
        }
    }
}