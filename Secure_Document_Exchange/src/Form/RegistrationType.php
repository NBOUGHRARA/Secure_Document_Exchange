<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Service\ProfilesService;

class RegistrationType extends AbstractType
{

    private $profilesService;

    public function __construct(ProfilesService $profilesService)
    {
        $this->profilesService = $profilesService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $this->profilesService->getProfilesForFormBuilder();
        $attributes = [
            'class' => 'form-control'
        ];

        $builder
            ->add('email', TextType::class, [
                'attr' => $attributes
            ])
            ->add('password', PasswordType::class, [
                'attr' => $attributes
            ])
            ->add('confirm_password', PasswordType::class, [
                'attr' => $attributes
            ])
            ->add('roles', ChoiceType::class, [
                'choices' =>$choices,
                'multiple' => true,
                'attr' => $attributes
            ])
            ->add('isDeleted', CheckboxType::class, [
                'required' => false
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
