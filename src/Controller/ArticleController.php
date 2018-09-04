<?php

namespace App\Controller;

use App\Entity\Article;
use App\Utils\Slugger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ArticleType;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class ArticleController
{
    /**
     * @Route("/{_locale}", name="homepage")
     */
    public function homepage(
        Request $request,
        EntityManagerInterface $entityManager,
        Environment $twig,
        string $homepageNumberOfArticles
    ) : Response
    {
        $languages = 'User preferred languages are: ' . implode(', ', $request->getLanguages());

        $articles = $entityManager->getRepository(Article::class)
            ->findMostRecent($homepageNumberOfArticles);

        $totalArticles = $entityManager->getRepository(Article::class)
            ->countArticles();

        return new Response($twig->render('homepage.html.twig', [
            'languages' => $languages,
            'articles' => $articles,
            'totalArticles' => $totalArticles,
        ]));
    }

    /**
     * @Route("/{_locale}/article/{slug}", name="article")
     */
    public function article(
        $slug,
        EntityManagerInterface $entityManager,
        Environment $twig,
        Request $request
    ) : Response
    {
        /** @var $article Article * */
        $article = $entityManager->getRepository(Article::class)
            ->findOneBySlug($slug);

        if (!$article) {
            return new Response('The article does not exist', 404);
        }

        $response = new Response($twig->render('article.html.twig', [
            'article' => $article,
        ]));

        $response->setEtag(md5($article->getSlug() . $request->getLocale()));
        $response->setLastModified($article->getDatePublished());
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        return $response;
    }

    /**
     * @Route("/{_locale}/add", name="add")
     */
    public function add(
        LoggerInterface $logger,
        Request $request,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        Environment $twig,
        Slugger $slugger,
        UrlGeneratorInterface $urlGenerator,
        Session $session
    ) : Response
    {
        $form = $formFactory->create(ArticleType::class);

        $logger->info('Display -Add an article- page');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setSlug($slugger->run($article->getTitle()));

            $entityManager->persist($article);
            $entityManager->flush();

            $session->getFlashBag()->add('success', 'flash.message.article.success');

            return new RedirectResponse(
                $urlGenerator->generate('article', ['slug' => $article->getSlug()])
            );
        }

        return new Response($twig->render('add.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    public function sidebar(
        EntityManagerInterface $entityManager,
        Environment $twig,
        $numberOfArticles
    ) : Response
    {
        $articles = $entityManager->getRepository(Article::class)
            ->findMostRecent($numberOfArticles);

        $response = new Response($twig->render('sidebar.html.twig', [
            'articles' => $articles,
        ]));

        $response->setPublic();
        $response->setSharedMaxAge(600);

        return $response;
    }
}
