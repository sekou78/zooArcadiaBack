<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/image')]
final class ImageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private ImageRepository $repository)
    {
        
    }
    #[Route(name: 'app_image_index', methods: ['GET'])]
    public function index(ImageRepository $imageRepository): Response
    {
        return $this->render('image/index.html.twig', [
            'images' => $imageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_image_new', methods: ['POST'])]
    public function new(): Response
    {
        $image = new Image();
        $image->setImageData(' ');
        $image->setCreatedAt(new \DateTimeImmutable());
        $image->setUpdatedAt(new \DateTimeImmutable());

        $this->manager->persist($image);
        $this->manager->flush();

        return $this->json(
            ['message' => "Image resource created with {$image->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'app_image_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $image = $this->repository->findOneBy(['id' => $id]);

        if (!$image) {
            throw $this->createNotFoundException("Pas d'image trouvé pour cet {$id} id");
        }

        return $this->json(
            ['message' => "l'image trouvé : 
            {$image->getImageData()}
            pour {$image->getId()} id"
        ]);
    }

    #[Route('/edit/{id}', name: 'app_image_edit', methods: ['PUT'])]
    public function edit(int $id): Response
    {
        $image = $this->repository->findOneBy(['id' => $id]);

        if (!$image) {
            throw $this->createNotFoundException("Pas d'image trouvé pour cet {$id} id");
        }

        $image->setImageData('modifié');
        $this->manager->flush();

        return $this->redirectToRoute('app_image_show', ['id' => $image->getId()]);
    }

    #[Route('/{id}', name: 'app_image_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $image = $this->repository->findOneBy(['id' => $id]);

        if (!$image) {
            throw $this->createNotFoundException("Pas d'image trouvé pour cet {$id} id");
        }

        $this->manager->remove($image);
        $this->manager->flush();

        return $this->redirectToRoute('app_image_index', [], Response::HTTP_SEE_OTHER);
    }
}
