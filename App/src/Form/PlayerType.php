<?php

namespace App\Form;

use App\Entity\Player;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname')
            ->add('firstname')
            ->add('position', TextType::class, [
                'required' => false, // Rendre le champ facultatif
            ])
            ->add('equipe')
            ->add('isCoach', CheckboxType::class, [
                'label' => 'Est coach ?',
                'required' => false,
            ])
            // Ajoutez d'autres champs ici...
        ;

        // Ajouter un événement pour modifier dynamiquement le champ 'position'
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $player = $event->getData();
            $form = $event->getForm();

            if ($player && $player->getIsCoach()) {
                // Si 'isCoach' est vrai, définissez automatiquement la valeur de 'position'
                $form->add('position', TextType::class, [
                    'required' => false,
                    'disabled' => true, // Désactiver le champ pour éviter toute modification manuelle
                    'data' => 'Entraîneur', // Valeur par défaut
                ]);
            } else {
                // Sinon, ajoutez normalement le champ 'position' avec la logique initiale
                $form->add('position', TextType::class, [
                    'required' => false,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Player::class,
        ]);
    }
}
