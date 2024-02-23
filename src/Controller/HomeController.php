<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        $articles = $this->entityManager->getRepository(Article::class)->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'articles'  => $articles,
        ]);
    }
}
