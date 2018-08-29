<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
    * @Route("/", name="homepage")
    */
    public function homepage()
    {
        return new Response('Homepage');
    }

    /**
     * @Route("/my-first-article", name="article")
     */
    public function article()
    {
        return new Response('Mon premier article');
    }
}
