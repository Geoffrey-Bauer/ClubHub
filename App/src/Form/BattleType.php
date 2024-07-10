<?php

namespace App\Form;

use App\Entity\Battle;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class BattleType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('date', DateTimeType::class, [
        'widget' => 'single_text',
        'html5' => true,
        'attr' => ['class' => 'form-control'],
        'model_timezone' => 'UTC',
        'view_timezone' => 'Europe/Paris',
      ])
      ->add('lieu', TextType::class, [
        'label' => 'Lieu du match',
      ])
      ->add('teamDomicile', EntityType::class, [
        'class' => Team::class,
        'choice_label' => 'name',
        'label' => 'Équipe à domicile',
      ])
      ->add('teamExterieur', EntityType::class, [
        'class' => Team::class,
        'choice_label' => 'name',
        'label' => 'Équipe à l\'extérieur',
        'constraints' => [
          new Callback([
            'callback' => function ($teamExterieur, ExecutionContextInterface $context) {
              $teamDomicile = $context->getRoot()->get('teamDomicile')->getData();
              if ($teamDomicile === $teamExterieur) {
                $context->buildViolation('Une équipe ne peut pas jouer contre elle-même.')
                  ->atPath('teamExterieur')
                  ->addViolation();
              }
            },
          ]),
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Battle::class,
    ]);
  }
}
