<?php

namespace App\Controller;

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
     * @Route("/my-first-article", name="article")
     */
    public function article()
    {
        return $this->render('article.html.twig', [
            'content' => 'Mon premier article',
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
}
