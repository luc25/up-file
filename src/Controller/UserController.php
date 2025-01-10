<?php

namespace App\Controller;

use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'profile', methods: ['GET', 'PUT'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user, ['method' => Request::METHOD_PUT]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if ($plainPassword != null) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }

            $entityManager->flush();

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/profile.html.twig', ['form' => $form]);
    }
}
