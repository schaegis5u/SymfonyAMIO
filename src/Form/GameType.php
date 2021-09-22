<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GameType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => "Titre du jeu"
            ])
            ->add('content', null, [
                'label' => "Description du jeu",
                'attr' => [
                    "rows" => 4
                ]
            ])
            ->add('enabled', ChoiceType::class, [
                'label' => "Publié",
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // Indiquer formulaire est lié à entité Game
        $resolver->setDefaults([
            'data_class' => Game::class //retourne chaine avec espace de nom vers cette classe
        ]);
    }
}