<?php

// src/Controller/ApkUploadController.php

namespace App\Controller;

use App\Form\ApkUploadType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\JsonResponse;

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
                $version = $form->get('version')->getData();
                if (!$apkFile) {
                        dump('Fichier non reçu');
                        dd($request->files->all());
                    }


                if ($apkFile) {
                    $destination = $this->getParameter('kernel.project_dir') . '/public';
                    $filenameVersioned = "app-$version.apk";
        $filenameLatest = "app-latest.apk";

        // Déplacement principal avec le nom versionné
        $apkFile->move($destination, $filenameVersioned);

        // Copie du fichier sous un autre nom
        copy("$destination/$filenameVersioned", "$destination/$filenameLatest");

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

    #[Route('/api/apks', name: 'apk_list')]
    public function listApks(): JsonResponse
    {
        $apkDir = __DIR__ . '/../../public';
        $files = glob($apkDir . '/*.apk');

        $versions = [];

        foreach ($files as $file) {
            $basename = basename($file);
            if (preg_match('/([\d.]+)\.apk$/', $basename, $matches)) {
                $versions[] = [
                    'filename' => $basename,
                    'version' => $matches[1],
                    'url' => '/'.$basename
                ];
            }
        }

        return new JsonResponse([
            'count' => count($versions),
            'apks' => $versions,
        ]);
    }
}
