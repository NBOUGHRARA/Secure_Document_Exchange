<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Service\ProfilesService;

class UserType extends AbstractType
{
    private $profilesService;

    public function __construct(ProfilesService $profilesService)
    {
        $this->profilesService = $profilesService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $this->profilesService->getProfilesForFormBuilder();
        $builder
            ->add('roles', ChoiceType::class, [
                'choices' =>$choices,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
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
