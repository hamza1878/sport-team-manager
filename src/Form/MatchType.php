<?php

namespace App\Form;

use App\Entity\Matchs;
use App\Entity\Equipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateMatch', DateType::class, ['widget' => 'single_text'])
            ->add('heure', TimeType::class, ['widget' => 'single_text'])
            ->add('stade', TextType::class)
            ->add('equipeDomicile', EntityType::class, ['class' => Equipe::class, 'choice_label' => 'nom'])
            ->add('equipeExterieur', EntityType::class, ['class' => Equipe::class, 'choice_label' => 'nom'])
            ->add('scoreDomicile', IntegerType::class)
            ->add('scoreExterieur', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Matchs::class,
        ]);
    }
}
