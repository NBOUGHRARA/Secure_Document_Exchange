<?php

namespace App\Form;

use App\Entity\Profiles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProfilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profileLabel', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Role'
            ])
            ->add('profileCode', TextType::class, [
                'attr' => [
                    'placeholder' => 'ROLE_',
                    'class' => 'form-control'
                ],
                'label' => 'code'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profiles::class,
        ]);
    }
}
