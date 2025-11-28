<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/users', name: 'admin_dashboard')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/change-role/{role}', name: 'admin_change_role')]
    public function changeRole(User $user, string $role, EntityManagerInterface $entityManager): Response
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('danger', 'You cannot modify another Administrator.');
            return $this->redirectToRoute('admin_dashboard');
        }

        if ($role === 'ROLE_USER') {
            $user->setRoles([]);
            $this->addFlash('success', 'User downgraded to standard User.');
        } 
        else {
            
            if (!in_array($role, ['ROLE_MODERATOR', 'ROLE_ADMIN'])) {
                 $this->addFlash('danger', 'Invalid role.');
                 return $this->redirectToRoute('admin_dashboard');
            }

            $currentRoles = $user->getRoles();
            if (!in_array($role, $currentRoles)) {
                $currentRoles[] = $role;
                $user->setRoles($currentRoles);
                $this->addFlash('success', 'User promoted to ' . $role);
            }
        }

        $entityManager->flush();
        return $this->redirectToRoute('admin_dashboard');
    }

}