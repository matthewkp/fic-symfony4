<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ArticleType;
use Psr\Log\LoggerInterface;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(Request $request)
    {
        $languages = 'User preferred languages are: ' . implode(', ', $request->getLanguages());

        return $this->render('homepage.html.twig', [
            'content' => 'Homepage',
            'languages' => $languages
        ]);
    }

    /**
     * @Route("/article/{id}", name="article")
     */
    public function article($id)
    {
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        if (!$article) {
            throw $this->createNotFoundException('The article does not exist');
        }

        return $this->render('article.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(LoggerInterface $logger, Request $request)
    {
        $form = $this->createForm(
            ArticleType::class,
            null,
            ['display_submit' => true]
        );

        $logger->info('Display -Add an article- page');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
        }

        return $this->render('add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function sidebar($numberOfArticles)
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findMostRecent($numberOfArticles);

        return $this->render('sidebar.html.twig', [
            'articles' => $articles,
        ]);
    }
}
