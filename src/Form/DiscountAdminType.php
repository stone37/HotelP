<?php

namespace App\Form;

use App\Entity\Discount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DiscountAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('discount', IntegerType::class, ['label' => 'Valeur'])
            ->add('code', TextType::class, ['label' => 'Code de réduction'])
            ->add('utilisation', IntegerType::class, ['label' => 'Nombre d\'utilisation (facultatif)'])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'placeholder' => 'Type',
                'choices' => [
                    'Prix fixe' => Discount::FIXED_PRICE,
                    'Pourcentage à déduire' => Discount::PERCENT_REDUCTION,
                ],
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary']
            ])
            ->add('enabled', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'label' => 'Activé',
                'placeholder' => 'Activé'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Discount::class
        ]);
    }
}
