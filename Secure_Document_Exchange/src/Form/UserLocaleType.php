<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserLocaleType extends AbstractType
{

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('locale', ChoiceType::class, [
            'choices' => [
                'English(US)'   => 'en_US',
                'Français'      => 'fr_FR',
                'Español'     => 'es_ES',
            ],
            'attr' => [
                'class' => 'form-control',
            ],
            'placeholder' => $this->translator->trans('Choose language'),
            'label' => $this->translator->trans('Available Languages')
        ])
        ->add('save', SubmitType::class, [
            "attr" => [
                "class" => "btn btn-success mt-2"
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
