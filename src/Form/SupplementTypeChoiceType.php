<?php

namespace App\Form;

use App\Entity\Supplement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupplementTypeChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                'Par jour' => Supplement::PER_DAY,
                'Par personne' => Supplement::PER_PERSON,
                'Par reservation' => Supplement::PER_BOOKING,
                'Par jour et par personne' => Supplement::PER_DAY_PERSON
            ],
            'choice_translation_domain' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'app_supplement_type_choice';
    }
}
