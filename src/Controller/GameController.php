<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/game")
 */
class GameController extends AbstractController{

    /**
     * @Route("/")
     */

    public function list(GameRepository $gameRepository): Response{
        $entities = $gameRepository->findAll(); // retourne tout les jeux

        return $this->render("game/list.html.twig", [
            'entities' => $entities
        ]);
    }

    /**
     * @Route("/new")
     */
    public function new(EntityManagerInterface $entityManager, Request $request) : Response {
        //EMI est un objet crée par Symfony pour nous
        $gameEntity = new Game;
        // Création formulaire avec classe GameType
        $form = $this->createForm(GameType::class, $gameEntity);

        // Injecter requête dans form pour récup données POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gameEntity); //Préparer requête
            $entityManager->flush(); //Executer requête
        }

        return $this->render("game/new.html.twig", [
            'form' => $form->createView(), //Envoyer vue formulaire dans vue twig
        ]);
    }
}
