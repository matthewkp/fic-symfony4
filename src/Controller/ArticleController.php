<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
    * @Route("/", name="homepage")
    */
    public function homepage(Request $request)
    {
        $languages = 'User preferred languages are: ' . implode(', ', $request->getLanguages());

        return new Response('Homepage <br>' . $languages);
    }

    /**
     * @Route("/my-first-article", name="article")
     */
    public function article()
    {
        return new Response('Mon premier article');
    }
}
