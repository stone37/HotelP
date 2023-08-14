<?php

namespace App\Form;

use App\Data\PromotionCrudData;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('description', CKEditorType::class, [
                'label' => false,
                'config' => ['height' => '150', 'uiColor' => '#ffffff', 'toolbar' => 'basic']
            ])
            ->add('start', DateType::class, ['label' => 'Date de Debut', 'widget' => 'single_text'])
            ->add('end', DateType::class, ['label' => 'Date de Fin', 'widget' => 'single_text'])
            ->add('discount', IntegerType::class, ['label' => 'Reduction (En %)'])
            ->add('enabled', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'label' => 'Activée',
                'placeholder' => 'Activée'
            ])
            ->add('room', RoomChoiceType::class, [
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'label' => 'Hébergement',
                'placeholder' => 'Hébergement'
            ])
            ->add('file', FileType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PromotionCrudData::class,
        ]);
    }
}
