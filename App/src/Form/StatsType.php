<?php

namespace App\Form;

use App\Entity\Stats;
use App\Entity\Player;
use App\Entity\Battle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatsType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('goal', IntegerType::class, [
        'label' => 'Buts',
      ])
      ->add('assists', IntegerType::class, [
        'label' => 'Passes décisives',
      ])
      ->add('yellowCard', IntegerType::class, [
        'label' => 'Cartons jaunes',
      ])
      ->add('redCard', IntegerType::class, [
        'label' => 'Cartons rouges',
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Stats::class,
    ]);
  }
}