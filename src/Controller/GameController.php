<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Like;
use App\Entity\User;
use App\Event\GameEvent;
use App\Event\GameEvents;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/game")
 */
class GameController extends AbstractController{

    /**
     * @Route("/")
     */

    public function list(GameRepository $gameRepository, Request $request): Response{
        $page = $request->get('p',1);
        $itemCount = 10;
        $search =$request->get('s', '');

        /*if ($this->getUser() instanceof User){
        $entities = $gameRepository->findAll(); // retourne tout les jeux
        $count = $gameRepository->count([]);
        } else { 
            $entities = $gameRepository->findEnabled();
            $count = $gameRepository->count(['enabled' => true]);
        }*/
        $entities = $gameRepository->findPagination($page, $itemCount, $search);

        $pageCount = \ceil($entities->count() / $itemCount);

        return $this->render("game/list.html.twig", [
            'entities' => $entities,//->getIterator(),
            'count' => $entities->count(),
            'pageCount' =>max($pageCount, 1),
        ]);
    }

    /**
     * @Route("/new")
     * @IsGranted("ROLE_USER")
     */
    public function new(EntityManagerInterface $entityManager, Request $request, TranslatorInterface $translatorInterface, EventDispatcherInterface $eventDispatcher) : Response {
        //EMI est un objet cr??e par Symfony pour nous
        $gameEntity = new Game;
        $gameEntity->setUser($this->getUser());
        // Cr??ation formulaire avec classe GameType
        $form = $this->createForm(GameType::class, $gameEntity);

        // Injecter requ??te dans form pour r??cup donn??es POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gameEntity); //Pr??parer requ??te
            $entityManager->flush(); //Executer requ??te

            $eventDispatcher->dispatch(new GameEvent($gameEntity), GameEvents::GAME_ADDED);

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
            $entityManager->persist($entity); //Pr??parer requ??te
            $entityManager->flush(); //Executer requ??te

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
            $entityManager->flush(); //Executer requ??te

            $this->addFlash('success', $translatorInterface->trans("game.delete.success", ["%game%" => $entity->getTitle()]) );
            return $this->redirectToRoute('app_game_list');
        }
        return $this->render("game/delete.html.twig", [
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/{id}/like", requirements={"id":"\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function like(Game $entity, LikeRepository $likeRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        $like = $likeRepository->findOneByUserAndGame($user, $entity);
        $active = true;

        if (null === $like)
        {
            $like = (new Like)
                ->setUser($user)
                ->setGame($entity)
                ->setDate(new \DateTime)
            ;

            $entityManager->persist($like);

        } else {
            $active = false;
            $entityManager->remove($like);
        }

        $entityManager->flush();

        if ($request->isXmlHttpRequest())
        {
            return new JsonResponse([
                'status' => 'success',
                'active' => $active,
            ]);
        }

        return $this->redirectToRoute('app_game_list');
    }
}
