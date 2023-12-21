<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Paragraph;
use App\Repository\ArticleRepository;
use App\Service\ReadingTimeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'article_index')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $this->json($articles);
    }

    #[Route('/new', name: 'article_new', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access this route.')]
    public function new(Request $request, EntityManagerInterface $entityManager, ReadingTimeService $readingTimeService): Response
    {
        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['title']) || !isset($requestData['name']) || !isset($requestData['description']) || !isset($requestData['paragraphs'])) {
            throw new \InvalidArgumentException('All of "title", "name", "description", and "paragraphs" must be provided for create.');
        }

        try {
            $article = new Article();
            $article->setName($requestData['name'])
                    ->setTitle($requestData['title'])
                    ->setDescription($requestData['description']);

            foreach ($requestData['paragraphs'] as $content) {
                $paragraph = new Paragraph();
                $paragraph->setContent($content['content']);
                $article->addParagraph($paragraph);
            }

            $readingTime = $readingTimeService->estimateReadingTime($article);
            $article->setReadingTime($readingTime);

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->json(['message' => 'Saved new article with id ' . $article->getId()]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An error occurred while saving the article.'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{slug}', name: 'article_show')]
    public function show(Article $article, ArticleRepository $articleRepository): JsonResponse
    {   
        $article = $articleRepository->findOneBy(['slug' => $article->getSlug()]);

        return $this->json($article);
    }

    

    #[Route('/{slug}/edit', name: 'article_edit', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access this route.')]
    public function update(Article $article, Request $request, EntityManagerInterface $entityManager, ReadingTimeService $readingTimeService): Response
    { 
        try {
            $requestData = json_decode($request->getContent(), true);
    
            if (!isset($requestData['title']) && !isset($requestData['name']) && !isset($requestData['description']) && !isset($requestData['paragraphs'])) {
                throw new \InvalidArgumentException('At least one of "title", "name", or "description" must be provided for update.');
            }

            if (isset($requestData['title']) && $requestData['title'] !== $article->getTitle()) {
                $article->setTitle($requestData['title']);
            }
    
            if (isset($requestData['name']) && $requestData['name'] !== $article->getName()) {
                $article->setName($requestData['name']);
            }
    
            if (isset($requestData['description']) && $requestData['description'] !== $article->getDescription()) {
                $article->setDescription($requestData['description']);
            }

            if (isset($requestData['paragraphs'])) {
                foreach ($article->getParagraphs() as $paragraph) {
                    foreach ($requestData['paragraphs'] as $key=>$value) {
                        if (isset($value['id']) && $paragraph->getId() == $value['id']) {
                            $paragraph->setContent($value['content']);
                            unset($requestData['paragraphs'][$key]);
                        }
                    }
                }
    
                foreach ($requestData['paragraphs'] as $content) {
                    $newParagraph = new Paragraph();
                    $newParagraph->setContent($content['content']);
                    $article->addParagraph($newParagraph);
                }
            }
            
            $article->setReadingTime($readingTimeService->estimateReadingTime($article));

            $entityManager->flush();

            return $this->json($article);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An error occurred while editing the article.'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{slug}', name: 'article_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access this route.')]
    public function delete(Article $article, EntityManagerInterface $entityManager): Response
    {
        try {
            $id = $article->getId();
            $entityManager->remove($article);
            $entityManager->flush();

            return $this->json(['message' => 'Removed article was id ' . $id]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An error occurred while removing the article.'], Response::HTTP_BAD_REQUEST);
        }
    }
}
