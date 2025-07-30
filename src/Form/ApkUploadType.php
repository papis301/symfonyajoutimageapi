<?php

// src/Form/ApkUploadType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ApkUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('apkFile', FileType::class, [
                'label' => 'Fichier APK',
                'mapped' => false,
                'required' => true,
                 'constraints' => [
                    new File([
                        'maxSize' => '100M',
                        'mimeTypes' => [
                            'application/vnd.android.package-archive',
                            'application/octet-stream',
                            'application/zip',
                        ],
                        'mimeTypesMessage' => 'Le fichier doit être un APK valide.',
                    ])
                ],
            ])
            ->add('version', TextType::class, [
                'label' => 'Version (ex: 1.2.3)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'La version est obligatoire.']),
                ]
            ]);
    }
}
