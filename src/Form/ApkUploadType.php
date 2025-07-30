<?php

// src/Form/ApkUploadType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

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
            ]);
    }
}
