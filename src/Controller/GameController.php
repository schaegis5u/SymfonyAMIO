<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Form\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/game")
 */
class GameController extends AbstractController{

    /**
     * @Route("/")
     */

    public function list(GameRepository $gameRepository): Response{
        if ($this->getUser() instanceof User){
        $entities = $gameRepository->findAll(); // retourne tout les jeux
        $count = $gameRepository->count([]);
        } else { 
            $entities = $gameRepository->findEnabled();
            $count = $gameRepository->count(['enabled' => true]);
        }

        return $this->render("game/list.html.twig", [
            'entities' => $entities,
            'count' => $count,
        ]);
    }

    /**
     * @Route("/new")
     * @IsGranted("ROLE_USER")
     */
    public function new(EntityManagerInterface $entityManager, Request $request, TranslatorInterface $translatorInterface) : Response {
        //EMI est un objet crée par Symfony pour nous
        $gameEntity = new Game;
        $gameEntity->setUser($this->getUser());
        // Création formulaire avec classe GameType
        $form = $this->createForm(GameType::class, $gameEntity);

        // Injecter requête dans form pour récup données POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gameEntity); //Préparer requête
            $entityManager->flush(); //Executer requête

            $this->addFlash('success', $translatorInterface->trans("game.new.success", ["%game%" => $gameEntity->getTitle()]) );
            return $this->redirectToRoute('app_game_list');
        }

        return $this->render("game/new.html.twig", [
            'form' => $form->createView(), //Envoyer vue formulaire dans vue twig
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id":"\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(EntityManagerInterface $entityManager, Game $entity, Request $request, TranslatorInterface $translatorInterface): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $entity);
        if (null === $entity->getUser()) {
            $entity->setUser($this->getUser());
        }
        $form = $this->createForm(GameType::class, $entity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity); //Préparer requête
            $entityManager->flush(); //Executer requête

            $this->addFlash('success', $translatorInterface->trans("game.edit.success", ["%game%" => $entity->getTitle()]) );
            return $this->redirectToRoute('app_game_list');
        }

        return $this->render("game/edit.html.twig", [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id":"\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(EntityManagerInterface $entityManager, Game $entity, Request $request, TranslatorInterface $translatorInterface): Response
    {
        if ($this->isCsrfTokenValid("delete".$entity->getId(), $request->get('token'))){
            $entityManager->remove($entity);
            $entityManager->flush(); //Executer requête

            $this->addFlash('success', $translatorInterface->trans("game.delete.success", ["%game%" => $entity->getTitle()]) );
            return $this->redirectToRoute('app_game_list');
        }
        return $this->render("game/delete.html.twig", [
            'entity' => $entity,
        ]);
    }
}
