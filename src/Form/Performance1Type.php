<?php

namespace App\Form;

use App\Entity\Joueur;
use App\Entity\Matchs;
use App\Entity\Performance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Performance1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minutesJouees')
            ->add('buts')
            ->add('passesDecisives')
            ->add('cartonJaune')
            ->add('cartonRouge')
            ->add('noteMatch')
            ->add('joueur', EntityType::class, [
                'class' => Joueur::class,
                'choice_label' => 'id',
            ])
            ->add('match', EntityType::class, [
                'class' => Matchs::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Performance::class,
        ]);
    }
}
