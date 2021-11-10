<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Security;

class MenuBuilder
{
    private $factory;
    private $security; //PHP7
    /**
     * Add any other dependency you need...
     */
    public function __construct(FactoryInterface $factory, Security $security /* private Security $security*/)
    {
        $this->factory = $factory;
        $this->security = $security; //PHP7
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', ['route' => 'app_app_home']);
        $menu->addChild('Games', ['route' => 'app_game_list']);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Supports', ['route' => 'support_index']);
            $menu->addChild('Categories', ['route' => 'category_index']);
        }
        // ... add more children

        return $menu;
    }

    public function createUserMenu(array $options): ItemInterface
    {
    $menu = $this->factory->createItem('root');

        if ($this->security->isGranted(('ROLE_USER'))) {
            $user = $this->security->getUser();
            $child = $menu->addChild($user->getEmail(), [
                'uri' => '#',
                'attributes' => [
                    'class' => 'menu-email',
                ]
            ]);
            $child->addChild('Logout', [
                'route' => 'app_logout',
                'attributes' => [
                    'class' => 'menu-register',
                ]
            ]);
        }
        else{
            $menu->addChild('Login', ['route' => 'app_login']);
            $menu->addChild('Register', [
                'route' => 'app_register',
                'attributes' => [
                    'class' => 'menu-register',
                ]
            ]);
    }
        // ... add more children

        return $menu;
    }
}