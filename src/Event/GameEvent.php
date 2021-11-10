<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Entity\Game;

class GameEvent extends Event
{
    /**
     * @var Game
     */
    private $game;

    public function __construct(Game $game)
    {
        return $this->game;
    }

    

    /**
     * Get the value of game
     *
     * @return  Game
     */ 
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set the value of game
     *
     * @param  Game  $game
     *
     * @return  self
     */ 
    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }
}

