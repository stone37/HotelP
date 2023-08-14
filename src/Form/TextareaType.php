<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextareaType as BaseTextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextareaType extends BaseTextareaType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => 'Description (facultatif)',
            'attr'  => ['class' => 'form-control md-textarea', 'rows'  => 4],
            'required' => false
        ]);
    }
}
