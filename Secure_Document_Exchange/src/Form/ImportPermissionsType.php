<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Service\ProfilesService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ImportPermissionsType extends AbstractType
{
    private $profilesService;

    public function __construct(ProfilesService $profilesService)
    {
        $this->profilesService = $profilesService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $availableProfiles = [
            'choices' => $this->profilesService->getProfilesForFormBuilder(),
            'multiple' => true,
            'attr' => [
                'class' => 'form-control'
            ],
            'label' => 'Profiles have permisssions to import files'
        ];
        $builder
            ->add('data', ChoiceType::class, $availableProfiles)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
