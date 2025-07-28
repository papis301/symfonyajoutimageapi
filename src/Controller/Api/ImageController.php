<?php

namespace App\Controller\Api;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/images')]
class ImageController extends AbstractController
{
    #[Route('', name: 'api_images_list', methods: ['GET'])]
    public function list(ImageRepository $repo): JsonResponse
    {
        $images = $repo->findAll();
        $data = array_map(fn($img) => [
            'id' => $img->getId(),
            'url' => '/uploads/images/' . $img->getFilename(),
        ], $images);

        return $this->json($data);
    }

    #[Route('/upload', name: 'api_images_upload', methods: ['POST'])]
    public function upload(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): JsonResponse
    {
        $file = $request->files->get('image');

        if (!$file) {
            return $this->json(['error' => 'No file uploaded'], 400);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($this->getParameter('images_directory'), $newFilename);

        $image = new Image();
        $image->setFilename($newFilename);
        $image->setCreatedAt(new \DateTimeImmutable());
        $em->persist($image);
        $em->flush();

        return $this->json(['success' => true, 'filename' => $newFilename]);
    }
}
