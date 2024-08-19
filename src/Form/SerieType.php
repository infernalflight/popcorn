<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('overview', TextareaType::class, [
                'label' => 'Résumé'
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'Returning',
                    'Terminé' => 'Ended',
                    'Abandonné' => 'Canceled',
                ],
                'placeholder' => ' -- Choisissez le statut -- ',
            ])
            ->add('vote')
            ->add('popularity')
            ->add('genres')
            ->add('firstAirDate', DateType::class, [
                'widget' => 'choice',
            ])
            ->add('lastAirDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('backdrop')
            ->add('poster')
            ->add('tmdbId')
            ->add('dateCreated', null, [
                'widget' => 'single_text',
            ])
            ->add('dateModified', null, [
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
