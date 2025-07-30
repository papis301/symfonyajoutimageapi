<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController
{
    #[Route('/version.json', name: 'app_version_json', methods: ['GET'])]
    public function version(): JsonResponse
    {
        // Tu peux plus tard rendre ces valeurs dynamiques via une base de données ou un fichier .env
        $latestVersion = '1.0.3';
        $downloadUrl = 'https://infosutils.deydem.pro/app-latest.apk';

        return new JsonResponse([
            'latest' => $latestVersion,
            'download_url' => $downloadUrl,
        ]);
    }
}
