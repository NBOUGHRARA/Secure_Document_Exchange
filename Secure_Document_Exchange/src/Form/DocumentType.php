<?php

namespace App\Form;

use App\Entity\Files;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Service\ProfilesService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DocumentType extends AbstractType
{

    private $profilesService;

    public function __construct(ProfilesService $profilesService)
    {
        $this->profilesService = $profilesService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $multiSelectOptions = [
            'choices' => $this->profilesService->getProfilesForFormBuilder(),
            'multiple' => true,
            'attr' => [
                'class' => 'form-control'
            ]
        ];

        $builder
            ->add('fileName', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('fileDirectory', FileType::class, [
                'label' => 'Choose file (PDF file)',
                'attr' => [
                    'class' => 'form-control'
                ],
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        //setting max size to 8 Mo
                        'maxSize' => '8192k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->add('canWrite', ChoiceType::class, $multiSelectOptions)
            ->add('canRead', ChoiceType::class, $multiSelectOptions)
            ->add('canDelete', ChoiceType::class, $multiSelectOptions)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Files::class,
        ]);
    }
}
