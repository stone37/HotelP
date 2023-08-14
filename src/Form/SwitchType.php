<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SwitchType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => ['Oui' => true, 'Non' => false],
            'expanded' => true,
            'multiple' => false,
            'required' => false,
            'choice_translation_domain' => false
        ]);
    }
}
