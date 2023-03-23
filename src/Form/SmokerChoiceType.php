<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SmokerChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                'Non-fumeurs' => 'Non-fumeurs',
                'Fumeurs' => 'Fumeurs',
                'Cet hébergement est fumeurs et non-fumeurs' => 'Cet hébergement est fumeurs et non-fumeurs',
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
        return 'app_smoker_choice';
    }
}
