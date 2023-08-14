<?php

namespace App\Form;

use App\Data\RoomCrudData;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de l\'hébergement'])
            ->add('roomNumber', IntegerType::class, ['label' => 'Nombre d\'hébergements (de ce type)'])
            ->add('price', IntegerType::class, ['label' => 'Tarif'])
            ->add('description', CKEditorType::class, [
                'label' => false,
                'config' => ['height' => '120', 'uiColor' => '#ffffff', 'toolbar' => 'basic'],
                'required' => false
            ])
            ->add('smoker', SmokerChoiceType::class, [
                'label' => 'Fumeurs ou non-fumeurs (facultatif)',
                'placeholder' => 'Fumeurs ou non-fumeurs (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'required' => false
            ])
            ->add('occupant', IntegerType::class, [
                'label' => 'Nombre d\'occupant max.',
                'help' => 'Indiquez le nombre de personnes maximum pouvant dormir dans cet hébergement.'
            ])
            ->add('area', IntegerType::class, [
                'label' => 'Superficie (facultatif)',
                'help' => 'Indiquez la superficie de l\'hébergement en m²',
                'required' => false
            ])
            ->add('enabled', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'label' => 'Activé',
                'placeholder' => 'Activé'
            ])
            ->add('couchage', TextType::class, [
                'label' => 'Couchage (facultatif)',
                'help' => 'Indiquez le type et le nombre lit de la chambre',
                'required' => false,
            ])
            ->add('taxe',  TaxeChoiceType::class, [
                'label' => 'Taxe (facultatif)',
                'placeholder' => 'Taxe (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'required' => false
            ])
            ->add('equipments', RoomEquipmentChoiceType::class, [
                'choice_attr' => function() {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Équipements de chambre (facultatif)',
                'expanded' => true,
                'multiple' => true,
                'required' => false
            ])
            ->add('supplements', SupplementChoiceType::class, [
                'choice_attr' => function() {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Suppléments (facultatif)',
                'expanded' => true,
                'multiple' => true,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RoomCrudData::class,
        ]);
    }
}
