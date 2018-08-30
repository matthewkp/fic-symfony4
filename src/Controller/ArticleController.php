<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ArticleType;
use Psr\Log\LoggerInterface;

class ArticleController extends AbstractController
{
    /**
     * @Route("/{_locale}", name="homepage")
     */
    public function homepage(Request $request) : Response
    {
        $languages = 'User preferred languages are: ' . implode(', ', $request->getLanguages());

        $totalArticles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->countArticles();

        return $this->render('homepage.html.twig', [
            'languages' => $languages,
            'totalArticles' => $totalArticles,
        ]);
    }

    /**
     * @Route("/{_locale}/article/{id}", name="article")
     */
    public function article($id) : Response
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
     * @Route("/{_locale}/add", name="add")
     */
    public function add(LoggerInterface $logger, Request $request) : Response
    {
        $form = $this->createForm(ArticleType::class);

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

    public function sidebar($numberOfArticles) : Response
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findMostRecent($numberOfArticles);

        return $this->render('sidebar.html.twig', [
            'articles' => $articles,
        ]);
    }
}
