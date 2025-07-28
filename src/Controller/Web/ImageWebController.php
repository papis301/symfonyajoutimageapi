<?php

namespace App\Controller\Web;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageWebController extends AbstractController
{
    #[Route('/upload', name: 'image_upload_form', methods: ['GET'])]
    public function uploadForm(): Response
    {
        return $this->render('upload.html.twig');
    }

    #[Route('/upload', name: 'image_upload_submit', methods: ['POST'])]
    public function handleUpload(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $file = $request->files->get('image');

        if ($file) {
            $safeName = $slugger->slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $newName = $safeName . '-' . uniqid() . '.' . $file->guessExtension();

            $file->move($this->getParameter('images_directory'), $newName);

            $image = new Image();
            $image->setFilename($newName);
            $image->setCreatedAt(new \DateTimeImmutable());
            $em->persist($image);
            $em->flush();

            $this->addFlash('success', 'Image bien enregistrée.');
        }

        return $this->redirectToRoute('image_upload_form');
    }

    #[Route('/gallery', name: 'image_gallery')]
        public function gallery(ImageRepository $repo): Response
        {
            $images = $repo->findAll();

            return $this->render('gallery.html.twig', [
                'images' => $images,
            ]);
        }
}
