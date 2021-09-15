<?php

namespace App\Controller;

use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
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
    public function new(EntityManagerInterface $entityManager) : Response {
        //EMI est un objet crée par Symfony pour nous
        $gameEntity = new Game;
        $gameEntity
            ->setTitle("Super Mario Bros")
            ->setContent("Jeu sorti en 1985")
            ->setEnabled(true)
            ->setCreatedAt(new \DateTime());

        $entityManager->persist($gameEntity); //Préparer requête
        $entityManager->flush(); //Executer requête

        return $this->render("game/new.html.twig");
    }
}
