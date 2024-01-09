<?php

namespace App\Controller;

use App\Entity\Paragraph;
use App\Repository\ParagraphRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('paragraphs')]
class ParagraphController extends AbstractController
{
    #[Route('/', name: 'paragraph_index')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {   
        $paragraphs = $entityManager->getRepository(Paragraph::class)->findAll();

        return $this->json($paragraphs);
    }

    #[Route('/{id}', name: 'paragraph_show')]
    public function show($id, ParagraphRepository $paragraphRepository): JsonResponse
    {   
        $paragraph = $paragraphRepository->findOneBy(['id' => $id]);

        if (!$paragraph) {
            throw $this->createNotFoundException('Paragraph not found');
        }

        return $this->json($paragraph, 200, [], ['groups' => ['paragraph']]);
    }

    #[Route('/{id}/edit', name: 'paragraph_edit', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access this route.')]
    public function update(Paragraph $paragraph, Request $request, EntityManagerInterface $entityManager): Response
    { 
        $requestData = json_decode($request->getContent(), true);
        
        if (!isset($requestData['content'])) {
            throw new \InvalidArgumentException('"Content" must be provided for update.');
        }

        if (isset($requestData['content']) && $requestData['content'] !== $paragraph->getContent()) {
            $paragraph->setContent($requestData['content']);
        }

        $entityManager->flush();

        return $this->json($paragraph);
        try {
        } catch (\Exception $e) {
            return $this->json(['error' => 'An error occurred while editing the paragraph.'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/delete', name: 'paragraph_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access this route.')]
    public function delete(Paragraph $paragraph, EntityManagerInterface $entityManager): Response
    {
        try {
            $id = $paragraph->getId();
            $entityManager->remove($paragraph);
            $entityManager->flush();

            return $this->json(['message' => 'Removed paragraph was id ' . $id]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An error occurred while removing the paragraph.'], Response::HTTP_BAD_REQUEST);
        }
    }
}
