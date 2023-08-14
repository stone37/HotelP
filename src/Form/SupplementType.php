<?php

namespace App\Form;

use App\Data\SupplementCrudData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupplementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('price', IntegerType::class, ['label' => 'Prix'])
            ->add('type', SupplementTypeChoiceType::class, [
                'label' => 'Par',
                'placeholder' => 'Par',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupplementCrudData::class,
        ]);
    }
}
