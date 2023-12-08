<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Paragraph;
use App\Service\SlugifyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'app_articles')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $this->json(['articles' => $articles]);
    }

    #[Route('/article', name: 'app_article', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $entityManager, SlugifyService $slugifyService): Response
    {
        $article = new Article();
        $requestData = json_decode($request->getContent(), true);
        $name = $requestData['name'];
        $title = $requestData['title'];
        $description = $requestData['description'];
        $paragraphs = $requestData['paragraphs'];
        
        $slug = $slugifyService->slugify($title);

        $article->setName($name);
        $article->setTitle($title);
        $article->setDescription($description);
        $article->setCreatedAt(new \DateTimeImmutable());
        $article->setSlug($slug);
        $article->setTitle($title);
        
        foreach ($paragraphs as $content) {
            $paragraph = new Paragraph();
            $paragraph->setContent($content);
            $article->addParagraph($paragraph);
        }

        $entityManager->persist($article);
        $entityManager->flush();

        return new Response('Saved new article with id '.$article->getId());
    }
}
