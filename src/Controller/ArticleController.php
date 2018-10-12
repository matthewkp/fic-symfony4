<?php

namespace App\Controller;

use App\Entity\Article;
use App\Utils\Slugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ArticleType;
use Psr\Log\LoggerInterface;

/**
 * @Route("/{_locale}", defaults={"_locale": "en"}, requirements={"_locale": "en|fr"})
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("", name="homepage")
     */
    public function homepage(Request $request, string $homepageNumberOfArticles) : Response
    {
        $languages = 'User preferred languages are: ' . implode(', ', $request->getLanguages());

        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findMostRecent($homepageNumberOfArticles);

        $totalArticles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->countArticles();

        return $this->render('homepage.html.twig', [
            'languages' => $languages,
            'articles' => $articles,
            'totalArticles' => $totalArticles,
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article")
     */
    public function article($slug) : Response
    {
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findOneBySlug($slug);

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
    public function add(LoggerInterface $logger, Request $request, Slugger $slugger) : Response
    {
        $article = new Article();
        $form = $this->createForm(
            ArticleType::class,
            $article,
            ['display_submit' => true]
        );

        $logger->info('Display -Add an article- page');

        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugger->run($article->getTitle()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article', ['slug' => $article->getSlug()]);
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
