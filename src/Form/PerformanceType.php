<?php

namespace App\Form;

use App\Entity\Performance;
use App\Entity\Joueur;
use App\Entity\Matchs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PerformanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('joueur', EntityType::class, ['class' => Joueur::class, 'choice_label' => 'nom'])
            ->add('match', EntityType::class, ['class' => Matchs::class, 'choice_label' => 'id'])
            ->add('minutesJouees', IntegerType::class)
            ->add('buts', IntegerType::class)
            ->add('passesDecisives', IntegerType::class)
            ->add('cartonJaune', IntegerType::class)
            ->add('cartonRouge', IntegerType::class)
            ->add('noteMatch', NumberType::class, ['scale' => 2]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Performance::class,
        ]);
    }
}
