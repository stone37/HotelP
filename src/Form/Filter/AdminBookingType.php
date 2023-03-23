<?php

namespace App\Form\Filter;

use App\Form\RoomChoiceType;
use App\Model\BookingSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('room', RoomChoiceType::class, [
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'label' => 'Type d\'hébergement',
                'placeholder' => 'Type d\'hébergement',
                'required' => false
            ])
            ->add('code', TextType::class, ['label' => 'Numéro de reservation', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BookingSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
