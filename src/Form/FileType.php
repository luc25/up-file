<?php

namespace App\Form;

use App\Constants\MimeTypes;
use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('file', SymfonyFileType::class, [
                'label' => 'File',
                'mapped' => false,
                'required' => $options['file_required'],
                'constraints' => [
                    new FileConstraint([
                        'maxSize' => '2048k',
                        'mimeTypes' => MimeTypes::getConstants(),
                        'mimeTypesMessage' => 'This file type is not allowed.',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
            'file_required' => true
        ]);

        $resolver->setAllowedTypes('file_required', 'bool');
    }
}
