<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/role')]
final class RoleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private RoleRepository $repository)
    {
        
    }
    #[Route(name: 'app_role_index', methods: ['GET'])]
    public function index(RoleRepository $roleRepository): Response
    {
        return $this->render('role/index.html.twig', [
            'roles' => $roleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_role_new', methods: ['POST'])]
    public function new(): Response
    {
        $role = new Role();
        $role->setLabel('le babel');
        $role->setCreatedAt(new \DateTimeImmutable());
        $role->setUpdatedAt(new \DateTimeImmutable());

        $this->manager->persist($role);
        $this->manager->flush();

        return $this->json(
            ['message' => "Role resource created with {$role->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'app_role_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $role = $this->repository->findOneBy(['id' => $id]);

        if (!$role) {
            throw $this->createNotFoundException("Pas de role trouvé pour cet {$id} id");
        }

        return $this->json(
            ['message' => "le role trouvé : 
            {$role->getLabel()}
            pour {$role->getId()} id"
        ]);
    }

    #[Route('/edit/{id}', name: 'app_role_edit', methods: ['PUT'])]
    public function edit(int $id): Response
    {
        $role = $this->repository->findOneBy(['id' => $id]);

        if (!$role) {
            throw $this->createNotFoundException("Pas d'role trouvé pour cet {$id} id");
        }

        $role->setLabel('Ma babel modifié');
        $this->manager->flush();

        return $this->redirectToRoute('app_role_show', ['id' => $role->getId()]);
    }

    #[Route('/{id}', name: 'app_role_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $role = $this->repository->findOneBy(['id' => $id]);

        if (!$role) {
            throw $this->createNotFoundException("Pas d'role trouvé pour cet {$id} id");
        }

        $this->manager->remove($role);
        $this->manager->flush();

        return $this->redirectToRoute('app_role_index', [], Response::HTTP_SEE_OTHER);
    }
}
