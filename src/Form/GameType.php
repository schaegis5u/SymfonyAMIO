<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Game;
use App\Entity\Support;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Knp\Menu\FactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Choice;


class GameType extends AbstractType{

    private $security;

    public function __construct(FactoryInterface $factory, Security $security /* private Security $security*/)
    {
        $this->security = $security; //PHP7
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label' => 'game.title'
            ])
            ->add('content', WysiwygType::class, [
                'label' => 'game.content',
                'attr' => [
                    "rows" => 4
                ]
            ])
            ->add('support', EntityType::class, [
                'class' => Support::class,
                'required' => false,
                'group_by' => 'constructor',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.year');
                },
            ]);

            if ($this->security->isGranted('ROLE_ADMIN')) {
                $builder->add('enabled', ChoiceType::class, [
                    'label' => 'game.enabled',
                    'choices' => [
                        'game.yes' => true,
                        'game.no' => false,
                    ],
                    'expanded' => true,
                ]);
            }

            $builder->add('categories', EntityType::class, [
                'label' => 'game.categories',
                'multiple' => true,
                'class' => Category::class,
            ])

            //Ajout du formulaire ImageType
            ->add('image', ImageType::class)

            ->add('deleteImage', CheckboxType::class, [
                'label' => 'game.delete_image',
                'required' => false,
            ])
        ;

        

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // Indiquer formulaire est li?? ?? entit?? Game
        $resolver->setDefaults([
            'data_class' => Game::class //retourne chaine avec espace de nom vers cette classe
        ]);
    }
}