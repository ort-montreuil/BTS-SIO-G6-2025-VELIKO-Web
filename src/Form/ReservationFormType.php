<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'), // Définit la date minimum à aujourd'hui
                ],
                'label' => 'Date de réservation',
            ])
            ->add('heureDebut', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de début'
            ])
            ->add('heureFin', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de fin'
            ])
            ->add('idStationDepart', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'name',
                'label' => 'Station de départ',
                'placeholder' => 'Sélectionnez une station',
                'required' => true,
                'mapped' => false, // Ne pas lier directement à l'entité Reservation
            ])
            ->add('idStationArrivee', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'name',
                'label' => 'Station d’arrivée',
                'placeholder' => 'Sélectionnez une station',
                'required' => true,
                'mapped' => false,
            ])
            ->add('typeVelo', ChoiceType::class, [
                'label' => 'Type de vélo',
                'choices' => [
                    'Électrique' => 'elec',
                    'Mécanique' => 'meca',
                ],
                'expanded' => true, // Affiche en checkbox
                'multiple' => false, // Un seul choix possible
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Reserver'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
