<?php

// src/Controller/ApkUploadController.php

namespace App\Controller;

use App\Form\ApkUploadType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApkUploadController extends AbstractController
{
    #[Route('/upload-apk', name: 'upload_apk')]
    public function upload(Request $request): Response
    {
        $form = $this->createForm(ApkUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $apkFile = $form->get('apkFile')->getData();
                if (!$apkFile) {
                        dump('Fichier non reçu');
                        dd($request->files->all());
                    }


                if ($apkFile) {
                    $destination = $this->getParameter('kernel.project_dir') . '/public';
                    $apkFile->move($destination, 'app-latest.apk');

                    $this->addFlash('success', 'Fichier APK uploadé avec succès !');
                    return $this->redirectToRoute('upload_apk');
                } else {
                    $this->addFlash('error', 'Aucun fichier reçu. Assurez-vous que le formulaire a bien enctype="multipart/form-data".');
                }
            } else {
                $this->addFlash('error', 'Formulaire invalide.');
            }
        }

        return $this->render('apk_upload/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
