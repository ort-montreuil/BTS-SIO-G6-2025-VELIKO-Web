<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilFormType extends AbstractType
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
            ->add('email',null, [
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
                'format' => 'yyyy-MM-dd',
                'label' => false,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}