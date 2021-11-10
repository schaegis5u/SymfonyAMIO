<?php

namespace App\EventSubscriber;

use App\Entity\Game;
use App\Event\GameEvent;
use App\Event\GameEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class GameSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            GameEvents::GAME_ADDED => 'onGameAdded'
        ];
    }

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onGameAdded(GameEvent $event): void
    {
        $game = $event->getGame();
        $email = (new Email())
            ->to(new Address('admin@pixel.fr'))
            ->subject('Nouveau jeu' . $game->getTitle())
            ->html("Un nouveau jeu a été ajouté")
            ->from(new Address('no-reply@pixel.fr'))
        ;

        $this->mailer->send($email);
    }
}