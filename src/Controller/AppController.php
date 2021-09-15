<?php

namespace App\Controller;

use Doctrine\Inflector\Rules\Transformation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController{
    /**
     * @Route("/")
     */
    public function home(): Response
    {
        return $this->render("app/home.html.twig", [
            'name' => 'Adolphe',
            'nationalité' => 'Belge',
        ]
    
        );
    }

    /**
     * @Route("/test")
     */
    public function front(): Response
    {
        return $this->render("app/test.twig");
    }
}

?>