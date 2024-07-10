<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\Training;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainingType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('date', DateTimeType::class, [
        'widget' => 'single_text',
        'html5' => true,
        'attr' => ['class' => 'form-control'],
        'model_timezone' => 'UTC',
        'view_timezone' => 'Europe/Paris',
      ])
      ->add('team', EntityType::class, [
        'class' => Team::class,
        'choice_label' => 'name',
        'label' => 'Équipe',
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Training::class,
    ]);
  }
}
