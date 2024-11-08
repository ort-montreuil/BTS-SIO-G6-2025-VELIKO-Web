<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom',null, [
                'label' => false,
            ])
            ->add('prenom', null,[
                'label' => false,
            ])
            ->add('ville',null, [
                'label' => false,
            ])
            ->add('codePostale',null, [
                'label' => false,
            ])
            ->add('adresse',null, [
                'label' => false,
            ])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'choice', // Use dropdowns for date selection
                'format' => 'yyyy-MM-dd',
                'years' => range(1950, date('Y')), // Set a 100-year range, starting from today
                'label' => 'Date de naissance', // Customize label if needed
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}