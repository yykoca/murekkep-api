<?php

namespace App\Controller;

use App\Entity\Paragraph;
use App\Repository\ParagraphRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('paragraph')]
class ParagraphController extends AbstractController
{
    #[Route('/', name: 'paragraph_index')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {   
        $paragraphs = $entityManager->getRepository(Paragraph::class)->findAll();

        return $this->json($paragraphs, 200, [], ['groups' => ['paragraph']]);
    }

    #[Route('/{id}', name: 'find_paragraph')]
    public function findOne($id, ParagraphRepository $paragraphRepository): JsonResponse
    {   
        $paragraph = $paragraphRepository->findOneBy(['id' => $id]);

        if (!$paragraph) {
            throw $this->createNotFoundException('Paragraph not found');
        }

        return $this->json($paragraph, 200, [], ['groups' => ['paragraph']]);
    }
}
