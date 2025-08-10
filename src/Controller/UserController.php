<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppEniAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/user', name: 'user_')]
final class UserController extends AbstractController
{
    #[Route('/users', name: 'users')]
    public function getUsers( UserRepository $userRepository ): Response
    {
        $users = $userRepository->findAll();
        $count = 0;
        return $this->render('user/users.html.twig',
            ['users' => $users, 'count' => $count]);
    }

    #[Route('/detail', name: 'detail')]
    public function detail(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $this->addFlash('success', 'Modification réussie ! Bienvenue.');

            return $security->login($user, AppEniAuthenticator::class, 'main');
        }

        return $this->render('user/detail.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
