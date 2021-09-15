<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/game")
 */
class GameController extends AbstractController{
    /**
     * @Route("/new")
     */
    public function new() : Response {
        return $this->render("game/new.html.twig");
    }
}
