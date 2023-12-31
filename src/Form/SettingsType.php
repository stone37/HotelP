<?php

namespace App\Form;

use App\Data\SettingsCrudData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, ['label' => 'Nom de l\'hotel'])
            ->add('email',TextType::class, ['label' => 'E-mail'])
            ->add('phone',TextType::class, ['label' => 'Telephone'])
            ->add('fax',TextType::class, ['label' => 'Fax (facultatif)', 'required' => false])
            ->add('address',TextType::class, ['label' => 'Adresse (facultatif)', 'required' => false])
            ->add('description', TextareaType::class, [
                'label' => 'Description (facultatif)',
                'attr'  => ['class' => 'form-control md-textarea', 'rows'  => 5],
                'required' => false
            ])
            ->add('country', TextType::class, ['label' => 'Pays (facultatif)', 'required' => false])
            ->add('city', TextType::class, ['label' => 'Ville (facultatif)', 'required' => false])
            ->add('facebookAddress', TextType::class, ['label' => 'Adresse Facebook (facultatif)', 'required' => false])
            ->add('twitterAddress', TextType::class, ['label' => 'Adresse Twitter (facultatif)', 'required' => false])
            ->add('linkedinAddress', TextType::class, ['label' => 'Adresse Linkedin (facultatif)', 'required' => false])
            ->add('instagramAddress', TextType::class, ['label' => 'Adresse Instagram (facultatif)', 'required' => false])
            ->add('youtubeAddress', TextType::class, ['label' => 'Adresse Youtube (facultatif)', 'required' => false])
            ->add('checkinTime', TimeType::class, ['label' => 'Heure d\'arrivée (facultatif)', 'widget' => 'single_text',])
            ->add('checkoutTime', TimeType::class, ['label' => 'Heure de Départ (facultatif)', 'widget' => 'single_text'])
            ->add('file', FileType::class, ['label' => 'Logo du site', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SettingsCrudData::class
        ]);
    }
}
